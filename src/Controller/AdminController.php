<?php

namespace App\Controller;

use App\Security\Permission;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminController extends AbstractController
{
    #[AdminRoute('/media', name: 'media')]
    #[IsGranted(Permission::MEDIA)]
    #[Template('admin/media.html.twig')]
    public function media(): void
    {
    }
}
