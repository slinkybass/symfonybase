<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
