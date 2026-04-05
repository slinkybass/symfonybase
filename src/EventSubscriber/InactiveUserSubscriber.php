<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Automatically logs out users that have been marked as inactive.
 */
class InactiveUserSubscriber implements EventSubscriberInterface
{
    private const LOGOUT_ROUTE = 'logout';

    public function __construct(
        private readonly Security $security,
    ) {
    }

    /**
     * Logs out the current user if they are marked as inactive.
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
