<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\ChangePasswordForm;
use App\Form\RegistrationForm;
use App\Form\ResetPasswordRequestForm;
use App\Repository\Filter\User as UserFilter;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Service\ConfigService;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

final class AuthController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $userRepo,
        private readonly ConfigService $configService,
        private readonly MailService $mailService,
        private readonly TranslatorInterface $translator,
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly VerifyEmailHelperInterface $verifyEmailHelper,
    ) {
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $config = $this->configService->get();

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
            'translation_domain' => 'admin',
            'favicon_path' => $config->appFavicon,
            'page_title' => $this->translator->trans('login_page.sign_in', [], 'EasyAdminBundle'),
            'csrf_token_intention' => 'authenticate',
            'target_path' => $this->generateUrl('home'),
            'username_label' => $this->translator->trans('entities.user.fields.email'),
            'remember_me_enabled' => true,
            'remember_me_checked' => true,
            'forgot_password_enabled' => $config->enableResetPassword,
            'forgot_password_path' => $this->generateUrl('reset_password_request'),
        ]);
    }

    #[Route('/reset-password-request', name: 'reset_password_request')]
    public function resetPasswordRequest(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordRequestForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $this->userRepo->filterOne([
                new UserFilter\EmailFilter($email),
            ]);

            $error = null;
            if ($user) {
                if (!$user->isActive()) {
                    $error = $this->translator->trans('app.messages.userDeactivated');
                } elseif (!$user->isVerified()) {
                    $error = $this->translator->trans('app.messages.userUnverified');
                }
            }

            if ($error) {
                $this->addFlash('error', $error);
            } else {
                return $this->sendResetPasswordRequestEmail($user);
            }
        }

        return $this->render('auth/reset_password_request.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/reset-password-request/sent', name: 'reset_password_request_sent')]
    public function resetPasswordRequestSent(): Response
    {
        // Validates that a reset token exists in session before showing the confirmation page.
        $this->getTokenObjectFromSession();
        $this->addFlash('success', $this->translator->trans('app.messages.resetPasswordRequestSent'));

        return $this->redirectToRoute('login');
    }

    #[Route('/reset-password/{token}', name: 'reset_password')]
    public function resetPassword(Request $request, UserPasswordHasherInterface $passwordHasher, ?string $token = null): Response
    {
        if ($token) {
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('reset_password');
        }

        $token = $this->getTokenFromSession();
        if ($token === null) {
            return $this->redirectToRoute('reset_password_request');
        }

        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('error', $this->translator->trans($e->getReason(), [], 'ResetPasswordBundle'));

            return $this->redirectToRoute('reset_password_request');
        }

        $form = $this->createForm(ChangePasswordForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->resetPasswordHelper->removeResetRequest($token);
            $encodedPassword = $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($encodedPassword);
            $this->em->persist($user);
            $this->em->flush();
            $this->cleanSessionAfterReset();
            $this->addFlash('success', $this->translator->trans('app.messages.resetPasswordDone'));

            return $this->redirectToRoute('login');
        }

        return $this->render('auth/reset_password.html.twig', [
            'form' => $form,
        ]);
    }

    private function sendResetPasswordRequestEmail(?User $user): RedirectResponse
    {
        // Redirect even if user not found to prevent email enumeration.
        if (!$user) {
            return $this->redirectToRoute('reset_password_request_sent');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->redirectToRoute('reset_password_request_sent');
        }

        $subject = $this->translator->trans('email.resetPasswordRequest.subject');
        $content = [
            $this->translator->trans('email.resetPasswordRequest.content1'),
            $this->translator->trans('email.resetPasswordRequest.content2'),
        ];
        $buttons = [
            $this->translator->trans('email.resetPasswordRequest.button') => $this->generateUrl('reset_password', ['token' => $resetToken->getToken()], UrlGeneratorInterface::ABSOLUTE_URL),
        ];
        $timeToExpire = $this->translator->trans($resetToken->getExpirationMessageKey(), $resetToken->getExpirationMessageData(), 'ResetPasswordBundle');
        $postContent = [
            $this->translator->trans('email.resetPasswordRequest.postContent1', ['%time%' => $timeToExpire]),
        ];
        $html = $this->renderView('mails/template.html.twig', ['subject' => $subject, 'content' => $content, 'buttons' => $buttons, 'postContent' => $postContent]);
        $emails = [$user->getEmail()];
        $this->mailService->send($subject, $html, $emails);

        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('reset_password_request_sent');
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var RoleRepository $roleRepo */
        $roleRepo = $this->em->getRepository(Role::class);
        $config = $this->configService->get();

        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($encodedPassword);
            $user->setRole($roleRepo->find($config->roleDefaultRegister->getId()));
            $user->setVerified(false);
            $this->em->persist($user);
            $this->em->flush();

            return $this->sendVerifyEmail($user);
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/verify', name: 'verify')]
    public function verify(Request $request): Response
    {
        $id = $request->query->getInt('id');
        if (!$id) {
            return $this->redirectToRoute('login');
        }
        $user = $this->userRepo->find($id);
        if (!$user) {
            return $this->redirectToRoute('login');
        }

        try {
            $this->verifyEmailHelper->validateEmailConfirmationFromRequest($request, (string) $user->getId(), (string) $user->getEmail());
            $user->setVerified(true);
            $this->em->persist($user);
            $this->em->flush();
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('verify_email_error', $this->translator->trans($e->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('login');
        }
        $this->addFlash('success', $this->translator->trans('app.messages.verifyDone'));

        return $this->redirectToRoute('login');
    }

    private function sendVerifyEmail(User $user): RedirectResponse
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature('verify', (string) $user->getId(), $user->getEmail(), ['id' => $user->getId()]);

        $subject = $this->translator->trans('email.verify.subject');
        $content = [
            $this->translator->trans('email.verify.content1'),
            $this->translator->trans('email.verify.content2'),
        ];
        $buttons = [
            $this->translator->trans('email.verify.button') => $signatureComponents->getSignedUrl(),
        ];
        $timeToExpire = $this->translator->trans($signatureComponents->getExpirationMessageKey(), $signatureComponents->getExpirationMessageData(), 'VerifyEmailBundle');
        $postContent = [
            $this->translator->trans('email.verify.postContent1', ['%time%' => $timeToExpire]),
        ];
        $html = $this->renderView('mails/template.html.twig', [
            'subject' => $subject,
            'content' => $content,
            'buttons' => $buttons,
            'postContent' => $postContent,
        ]);
        $emails = [$user->getEmail()];
        $this->mailService->send($subject, $html, $emails);

        $this->addFlash('success', $this->translator->trans('app.messages.verifySended'));

        return $this->redirectToRoute('home');
    }
}
