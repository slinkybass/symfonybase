<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Automatically logs out inactive users on each request.
 * This subscriber checks if the current user is active, and if not, logs them out.
 */
class InactiveUserSubscriber implements EventSubscriberInterface
{
    private const LOGOUT_ROUTE = 'logout';

    public function __construct(
        private Security $security,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

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
