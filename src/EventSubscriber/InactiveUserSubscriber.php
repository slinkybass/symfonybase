<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Forces logout for inactive `User` accounts on every main request except the `logout` route.
 */
final class InactiveUserSubscriber implements EventSubscriberInterface
{
    private const LOGOUT_ROUTE = 'logout';

    public function __construct(
        private readonly Security $security,
    ) {
    }

    /**
     * Replaces the kernel response with the Security logout response so inactive users cannot browse authenticated routes.
     */
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

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 5],
        ];
    }
}
