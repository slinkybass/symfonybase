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
 * Request-time access rules: public/auth/register/reset/privacy/cookies routes vs `AppConfig` and authentication.
 *
 * Runs only on the main request (priority 7). Extend the route name constants when adding routes under the same policy.
 */
final class AccessSubscriber implements EventSubscriberInterface
{
    private const PUBLIC_ROUTES = [
        'home',
    ];
    private const LOGIN_ROUTES = [
        'login',
    ];
    private const REGISTER_ROUTES = [
        'register',
        'verify',
    ];
    private const RESET_ROUTES = [
        'reset_password_request',
        'reset_password_request_sent',
        'reset_password',
    ];
    private const PRIVACY_ROUTE = 'privacy';
    private const COOKIES_ROUTE = 'cookies';

    public function __construct(
        private readonly RouterInterface $router,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly ConfigService $configService,
    ) {
    }

    /**
     * Short-circuits the request with a redirect when the route and auth/config combination is not allowed.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');

        if (!$routeName || !$this->router->getRouteCollection()->get($routeName)) {
            return;
        }

        $config = $this->configService->get();
        $isLogged = $this->authorizationChecker->isGranted('IS_AUTHENTICATED');
        $isAdmin = $this->authorizationChecker->isGranted('ROLE_ADMIN');
        $redirect = null;

        if (in_array($routeName, self::PUBLIC_ROUTES, true)) {
            if ($isAdmin) {
                $redirect = $this->router->generate('admin');
            } elseif (!$isLogged && !$config->enablePublic) {
                $redirect = $this->router->generate('login');
            }
        } elseif (in_array($routeName, self::LOGIN_ROUTES, true)) {
            if ($isLogged) {
                $redirect = $this->router->generate('home');
            }
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
        } elseif ($routeName === self::PRIVACY_ROUTE) {
            if (($config->privacyText ?? '') === '') {
                $redirect = $this->router->generate('home');
            }
        } elseif ($routeName === self::COOKIES_ROUTE) {
            if (!$config->enableCookies || ($config->cookiesText ?? '') === '') {
                $redirect = $this->router->generate('home');
            }
        }

        if ($redirect !== null) {
            $event->setResponse(new RedirectResponse($redirect));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 7],
        ];
    }
}
