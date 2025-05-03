<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AuthController extends AbstractController
{
	private $translator;

	public function __construct(TranslatorInterface $translator) {
		$this->translator = $translator;
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
			'csrf_token_intention' => 'authenticate',
			'target_path' => $this->generateUrl('home'),
			'username_label' => $this->translator->trans('entities.user.fields.email'),
			'remember_me_enabled' => true,
			'remember_me_checked' => true,
		]);
    }
}
