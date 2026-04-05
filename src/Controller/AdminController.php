<?php

namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class AdminController extends AbstractController
{
    #[AdminRoute('/media', name: 'media')]
    public function media(): Response
    {
        return $this->render('admin/media.html.twig');
    }
}
