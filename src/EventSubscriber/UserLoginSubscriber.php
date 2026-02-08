<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Prevents login if the user is inactive or not verified.
 */
class UserLoginSubscriber implements EventSubscriberInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        /** @var SessionInterface $session */
        $session = $event->getRequest()->getSession();

        $error = null;
        if (!$user->isActive()) {
            $error = $this->translator->trans('app.messages.userDeactivated');
        } elseif (!$user->isVerified()) {
            $error = $this->translator->trans('app.messages.userUnverified');
        }

        if ($error) {
            $session->getFlashBag()->add('danger', $error);
            throw new DisabledException($error);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        ];
    }
}
