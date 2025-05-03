<?php

namespace App\EventSubscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PublicAccessSubscriber implements EventSubscriberInterface
{
	private $router;
	private $authorizationChecker;
	private $security;

	public function __construct(RouterInterface $router, AuthorizationCheckerInterface $authorizationChecker, Security $security)
	{
		$this->router = $router;
		$this->authorizationChecker = $authorizationChecker;
		$this->security = $security;
	}

	public function onKernelRequest(RequestEvent $event)
	{
		$user = $this->security->getUser();
		$request = $event->getRequest();
		$routeName = $request->attributes->get('_route');
		$availablePublicRoutesForAdmins = [];

		if ($routeName) {
			$routePath = $this->router->getRouteCollection()->get($routeName)->getDefault('_controller');
			if ($routePath) {
				$controllerName = strstr(substr(strrchr($routePath, '\\'), 1), '::', true);
				if ($controllerName) {
					if (($controllerName == "PublicController" || $controllerName == "AuthController") && $user) {
						if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
							if (!in_array($routeName, $availablePublicRoutesForAdmins)) {
								$event->setResponse(new RedirectResponse($this->router->generate('admin')));
							}
						} else {
							$event->setResponse(new RedirectResponse($this->router->generate('home')));
						}
					}
				}
			}
		}
		return;
	}

	public static function getSubscribedEvents()
	{
		return [
			KernelEvents::REQUEST => [['onKernelRequest', -100]],
		];
	}
}
