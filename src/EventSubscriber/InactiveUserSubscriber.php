<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Forces logout for inactive `User` accounts on every main request except the `logout` route.
 */
final class InactiveUserSubscriber
{
    private const LOGOUT_ROUTE = 'logout';

    public function __construct(
        private readonly Security $security,
    ) {
    }

    /**
     * Replaces the kernel response with the Security logout response so inactive users cannot browse authenticated routes.
     */
    #[AsEventListener(priority: 5)]
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        $route = $event->getRequest()->attributes->get('_route');

        if ($user instanceof User && !$user->isActive() && $route !== self::LOGOUT_ROUTE) {
            $event->setResponse($this->security->logout(false));
        }
    }
}
