<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin/home', name: 'admin_home')]
    public function home(): Response
    {
        return $this->render('admin/home.html.twig');
    }

    #[Route('/admin/media', name: 'admin_media')]
    public function admin_media(): Response
    {
        return $this->render('admin/media.html.twig');
    }
}
