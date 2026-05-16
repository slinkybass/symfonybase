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
 * After interactive login, rejects `User` accounts that are inactive or not email-verified.
 *
 * Flashes a translated error when a session exists, then throws `DisabledException` so Symfony treats the login as failed.
 */
final class UserLoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @throws DisabledException when the authenticated principal is inactive or unverified
     */
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
