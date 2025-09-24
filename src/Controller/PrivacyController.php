<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{
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
