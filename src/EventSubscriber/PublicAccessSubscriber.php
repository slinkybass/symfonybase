<?php

namespace App\EventSubscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Redirects users accessing public routes based on config settings and user roles.
 */
class PublicAccessSubscriber implements EventSubscriberInterface
{
    private RouterInterface $router;
    private AuthorizationCheckerInterface $authorizationChecker;
    private Security $security;

    public function __construct(RouterInterface $router, AuthorizationCheckerInterface $authorizationChecker, Security $security)
    {
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
        $this->security = $security;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $request = $event->getRequest();
        $session = $request->getSession();
        $availablePublicRoutes = [];

        $routeName = $request->attributes->get('_route');
        if (!$routeName) {
            return;
        }

        $routePath = $this->router->getRouteCollection()->get($routeName)->getDefault('_controller');
        if (!$routePath) {
            return;
        }

        $controllerName = strstr(substr(strrchr($routePath, '\\'), 1), '::', true);
        if (!$controllerName) {
            return;
        }

        $redirect = null;

        if ($controllerName == "PublicController" && !in_array($routeName, $availablePublicRoutes)) {
            if ($user && $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $redirect = $this->router->generate('admin');
            } elseif (!$session->get('config')->enablePublic) {
                $redirect = $this->router->generate('login');
            }
        } elseif ($controllerName == "AuthController" && $user) {
            $redirect = $this->router->generate('home');
        }

        if ($redirect) {
            $event->setResponse(new RedirectResponse($redirect));
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
