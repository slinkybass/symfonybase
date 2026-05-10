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
final class UserLoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();

        $error = match (true) {
            !$user->isActive() => $this->translator->trans('app.messages.userDeactivated'),
            !$user->isVerified() => $this->translator->trans('app.messages.userUnverified'),
            default => null,
        };

        if ($error === null) {
            return;
        }

        $request = $event->getRequest();
        if ($request->hasSession()) {
            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->add('error', $error);
        }
        throw new DisabledException($error);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => ['onSecurityInteractiveLogin', 0],
        ];
    }
}
