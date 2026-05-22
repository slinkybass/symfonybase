<?php

namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AdminController extends AbstractController
{
    #[AdminRoute('/media', name: 'media')]
    #[Template('admin/media.html.twig')]
    public function media(): void
    {
    }
}
