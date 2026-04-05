<?php

namespace App\EventSubscriber;

use App\Service\ConfigService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Redirects users based on public access settings, authentication status and role.
 */
class PublicAccessSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly Security $security,
        private readonly ConfigService $configService,
    ) {
    }

    /**
     * Redirects the request based on controller, user authentication and public access config.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');

        if (!$routeName) {
            return;
        }

        $route = $this->router->getRouteCollection()->get($routeName);

        if (!$route) {
            return;
        }

        $controller = $route->getDefault('_controller');
        $controllerName = $this->resolveControllerName($controller);

        if (!$controllerName) {
            return;
        }

        $user = $this->security->getUser();
        $config = $this->configService->get();
        $redirect = null;

        if ($controllerName === 'PublicController') {
            if ($user && $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $redirect = $this->router->generate('admin');
            } elseif (!$config->enablePublic) {
                $redirect = $this->router->generate('login');
            }
        } elseif ($controllerName === 'AuthController' && $user) {
            $redirect = $this->router->generate('home');
        }

        if ($redirect) {
            $event->setResponse(new RedirectResponse($redirect));
        }
    }

    /**
     * Resolves the short controller class name from a fully qualified controller string.
     *
     * @param string|null $controller the fully qualified controller string (e.g. 'App\Controller\PublicController::index')
     */
    private function resolveControllerName(?string $controller): ?string
    {
        if (!$controller) {
            return null;
        }

        $class = strstr($controller, '::', true) ?: $controller;

        return substr(strrchr($class, '\\'), 1) ?: null;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 7],
        ];
    }
}
