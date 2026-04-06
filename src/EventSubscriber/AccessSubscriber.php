<?php

namespace App\EventSubscriber;

use App\Service\ConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Controls access to public, auth and privacy routes based on authentication status,
 * user role and application configuration.
 */
class AccessSubscriber implements EventSubscriberInterface
{
    private const LOGIN_ROUTES = ['login'];
    private const REGISTER_ROUTES = ['register', 'verify'];
    private const RESET_ROUTES = ['reset', 'reset_sent', 'reset_token'];

    public function __construct(
        private readonly RouterInterface $router,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly ConfigService $configService,
    ) {
    }

    /**
     * Redirects the request based on controller, authentication status and config.
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

        $controllerName = $this->resolveControllerName($route->getDefault('_controller'));

        if (!$controllerName) {
            return;
        }

        $config = $this->configService->get();
        $isLogged = $this->authorizationChecker->isGranted('IS_AUTHENTICATED');
        $isAdmin = $this->authorizationChecker->isGranted('ROLE_ADMIN');
        $redirect = null;

        switch ($controllerName) {
            case 'PublicController':
                if ($isAdmin) {
                    $redirect = $this->router->generate('admin');
                } elseif (!$isLogged && !$config->enablePublic) {
                    $redirect = $this->router->generate('login');
                }
                break;
            case 'AuthController':
                if (in_array($routeName, self::LOGIN_ROUTES, true) && $isLogged) {
                    $redirect = $this->router->generate('home');
                } elseif (in_array($routeName, self::REGISTER_ROUTES, true)) {
                    if ($isLogged) {
                        $redirect = $this->router->generate('home');
                    } elseif (!$config->enableRegister) {
                        $redirect = $this->router->generate('login');
                    }
                } elseif (in_array($routeName, self::RESET_ROUTES, true)) {
                    if ($isLogged) {
                        $redirect = $this->router->generate('home');
                    } elseif (!$config->enableResetPassword) {
                        $redirect = $this->router->generate('login');
                    }
                }
                break;
            case 'PrivacyController':
                if ($routeName === 'privacy' && empty($config->privacyText)) {
                    $redirect = $this->router->generate('home');
                } elseif ($routeName === 'cookies' && (!$config->enableCookies || empty($config->cookiesText))) {
                    $redirect = $this->router->generate('home');
                }
                break;
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
