<?php

namespace App\EventSubscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Logs out inactive users automatically on each request.
 */
class InactiveUserSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        if ($user && !$user->isActive() && $route !== 'logout') {
            $event->setResponse($this->security->logout(false));
            return;
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', -100]],
        ];
    }
}
