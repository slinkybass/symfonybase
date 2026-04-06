<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordForm;
use App\Form\RegistrationForm;
use App\Form\ResetPasswordRequestForm;
use App\Repository\Filter\User as UserFilter;
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
            'forgot_password_path' => $this->generateUrl('reset'),
        ]);
    }

    #[Route('/reset-password-request', name: 'reset')]
    public function reset(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordRequestForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $this->em->getRepository(User::class)->filter([
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
                return $this->processSendingPasswordReset($user);
            }
        }

        return $this->render('public/reset/reset.html.twig', [
            'requestForm' => $form,
        ]);
    }

    #[Route('/reset-password-request/sent', name: 'reset_sent')]
    public function resetSent(): Response
    {
        $this->getTokenObjectFromSession();
        $this->addFlash('success', $this->translator->trans('app.messages.resetPasswordSended'));

        return $this->redirectToRoute('login');
    }

    #[Route('/reset-password/{token}', name: 'reset_token')]
    public function resetToken(Request $request, UserPasswordHasherInterface $passwordHasher, ?string $token = null): Response
    {
        if ($token) {
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('reset_token');
        }

        $token = $this->getTokenFromSession();
        if ($token === null) {
            return $this->redirectToRoute('reset');
        }

        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('error', $this->translator->trans($e->getReason(), [], 'ResetPasswordBundle'));

            return $this->redirectToRoute('reset');
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

        return $this->render('public/reset/token.html.twig', [
            'resetForm' => $form,
        ]);
    }

    private function processSendingPasswordReset(?User $user): RedirectResponse
    {
        if (!$user) {
            return $this->redirectToRoute('reset_sent');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->redirectToRoute('reset_sent');
        }

        $subject = $this->translator->trans('email.reset.subject');
        $content = [
            $this->translator->trans('email.reset.content1'),
            $this->translator->trans('email.reset.content2'),
        ];
        $buttons = [
            $this->translator->trans('email.reset.button') => $this->generateUrl('reset_token', ['token' => $resetToken->getToken()], UrlGeneratorInterface::ABSOLUTE_URL),
        ];
        $timeToExpire = $this->translator->trans($resetToken->getExpirationMessageKey(), $resetToken->getExpirationMessageData(), 'ResetPasswordBundle');
        $postContent = [
            $this->translator->trans('email.reset.postContent1', ['%time%' => $timeToExpire]),
        ];
        $html = $this->renderView('mails/template.html.twig', ['subject' => $subject, 'content' => $content, 'buttons' => $buttons, 'postContent' => $postContent]);
        $emails = [$user->getEmail()];
        $this->mailService->send($subject, $html, $emails);

        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('reset_sent');
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $config = $this->configService->get();
        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($encodedPassword);
            $user->setRole($config->roleDefaultRegister);
            $user->setVerified(false);
            $this->em->persist($user);
            $this->em->flush();

            return $this->processSendingVerifyEmail($user);
        }

        return $this->render('public/register/register.html.twig', [
            'registerForm' => $form,
        ]);
    }

    #[Route('/verify', name: 'verify')]
    public function verify(Request $request): Response
    {
        $id = $request->query->get('id');
        if (!$id) {
            return $this->redirectToRoute('register');
        }
        $user = $this->em->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->redirectToRoute('register');
        }

        try {
            $this->verifyEmailHelper->validateEmailConfirmationFromRequest($request, (string) $user->getId(), (string) $user->getEmail());
            $user->setVerified(true);
            $this->em->persist($user);
            $this->em->flush();
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('verify_email_error', $this->translator->trans($e->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('register');
        }
        $this->addFlash('success', $this->translator->trans('app.messages.verifyDone'));

        return $this->redirectToRoute('home');
    }

    private function processSendingVerifyEmail(User $user): RedirectResponse
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
        $html = $this->container->get('twig')->render('mails/template.html.twig', ['subject' => $subject, 'content' => $content, 'buttons' => $buttons, 'postContent' => $postContent]);
        $emails = [$user->getEmail()];
        $this->mailService->send($subject, $html, $emails);

        $this->addFlash('success', $this->translator->trans('app.messages.verifySended'));

        return $this->redirectToRoute('home');
    }
}
