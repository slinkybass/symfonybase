<?php

namespace App\Controller;

use App\Service\ConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PrivacyController extends AbstractController
{
    private ConfigService $configService;

    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    #[Route('/privacy', name: 'privacy')]
    public function privacy(Request $request): Response
    {
        $config = $this->configService->get();

        if (empty($config->privacyText)) {
            return $this->redirectToRoute('home');
        }

        return $this->render('public/privacy/privacy.html.twig');
    }

    #[Route('/cookies', name: 'cookies')]
    public function cookies(Request $request): Response
    {
        $config = $this->configService->get();

        if ($config->enableCookies === false || empty($config->cookiesText)) {
            return $this->redirectToRoute('home');
        }

        return $this->render('public/privacy/cookies.html.twig');
    }
}
