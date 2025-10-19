<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordForm;
use App\Form\ResetPasswordRequestForm;
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

use function Symfony\Component\Translation\t;

final class AuthController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private EntityManagerInterface $em;
    private MailService $mailService;
    private TranslatorInterface $translator;
    private ResetPasswordHelperInterface $resetPasswordHelper;

    public function __construct(EntityManagerInterface $em, MailService $mailService, TranslatorInterface $translator, ResetPasswordHelperInterface $resetPasswordHelper)
    {
        $this->em = $em;
        $this->mailService = $mailService;
        $this->translator = $translator;
        $this->resetPasswordHelper = $resetPasswordHelper;
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $session = $this->container->get('request_stack')->getSession();

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
            'translation_domain' => 'admin',
            'favicon_path' => $session->get('config')->appFavicon,
            'page_title' => t('login_page.sign_in', [], 'EasyAdminBundle'),
            'csrf_token_intention' => 'authenticate',
            'target_path' => $this->generateUrl('home'),
            'username_label' => t('entities.user.fields.email'),
            'remember_me_enabled' => true,
            'remember_me_checked' => true,
            'forgot_password_enabled' => $session->get('config')->enableResetPassword,
            'forgot_password_path' => $this->generateUrl('reset'),
        ]);
    }

    #[Route('/reset-password-request', name: 'reset')]
    public function request(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordRequestForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $this->em->getRepository(User::class)->findOneBy([
                'email' => $email
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
                $this->addFlash('danger', $error);
            } else {
                return $this->processSendingPasswordReset($user);
            }
        }

        return $this->render('public/reset/reset.html.twig', [
            'requestForm' => $form,
        ]);
    }

    #[Route('/reset-password-request/sent', name: 'reset_sent')]
    public function checkEmail(): Response
    {
        $this->getTokenObjectFromSession();
        $this->addFlash('success', $this->translator->trans('app.messages.resetPasswordSended'));
        return $this->redirectToRoute('login');
    }

    #[Route('/reset-password/{token}', name: 'reset_token')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, ?string $token = null): Response
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
            $this->translator->trans('email.reset.button') => $this->generateUrl('reset_token', ['token' => $resetToken->getToken()], UrlGeneratorInterface::ABSOLUTE_URL)
        ];
        $timeToExpire = t($resetToken->getExpirationMessageKey(), $resetToken->getExpirationMessageData(), 'ResetPasswordBundle');
        $postContent = [
            $this->translator->trans('email.reset.postContent1', ['%time%' => $timeToExpire]),
        ];
        $html = $this->renderView('mails/template.html.twig', ['subject' => $subject, 'content' => $content, 'buttons' => $buttons, 'postContent' => $postContent]);
        $emails = [$user->getEmail()];
        $this->mailService->sendEmail($subject, $html, $emails);

        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('reset_sent');
    }
}
