<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

/** Legal and cookie policy pages; visibility and content are also gated in `AccessSubscriber` via `AppConfig`. */
final class PrivacyController extends AbstractController
{
    #[Route('/privacy', name: 'privacy')]
    #[Template('privacy/privacy.html.twig')]
    public function privacy(): void
    {
    }

    #[Route('/cookies', name: 'cookies')]
    #[Template('privacy/cookies.html.twig')]
    public function cookies(): void
    {
    }
}
