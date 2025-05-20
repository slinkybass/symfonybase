<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use function Symfony\Component\Translation\t;

final class AuthController extends AbstractController
{
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
        ]);
    }

    #[Route('/privacy', name: 'privacy')]
    public function privacy(Request $request): Response
    {
        $session = $this->container->get('request_stack')->getSession();
        $configSession = $session->get('config');

        if (empty($configSession->privacyText)) {
            return $this->redirectToRoute('home');
        }
        return $this->render('public/privacy/privacy.html.twig');
    }

    #[Route('/cookies', name: 'cookies')]
    public function cookies(Request $request): Response
    {
        $session = $this->container->get('request_stack')->getSession();
        $configSession = $session->get('config');

        if ($configSession->enableCookies === false || empty($configSession->cookiesText)) {
            return $this->redirectToRoute('home');
        }
        return $this->render('public/privacy/cookies.html.twig');
    }
}
