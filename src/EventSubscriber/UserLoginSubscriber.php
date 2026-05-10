<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Prevents login if the user is inactive or not verified.
 */
class UserLoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();

        $error = null;
        if (!$user->isActive()) {
            $error = $this->translator->trans('app.messages.userDeactivated');
        } elseif (!$user->isVerified()) {
            $error = $this->translator->trans('app.messages.userUnverified');
        }

        if ($error !== null) {
            $request = $event->getRequest();
            if ($request->hasSession()) {
                /** @var Session $session */
                $session = $request->getSession();
                $session->getFlashBag()->add('error', $error);
            }
            throw new DisabledException($error);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => ['onSecurityInteractiveLogin', 0],
        ];
    }
}
