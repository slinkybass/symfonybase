<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    /** @var string[] */
    private array $locales;

    public function __construct(
        private string $defaultLocale,
        string $locales,
    ) {
        $this->locales = explode('|', $locales);
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->hasSession()) {
            return;
        }

        $session = $request->getSession();
        $requestLocale = $request->query->get('_locale');

        if ($requestLocale && in_array($requestLocale, $this->locales, true)) {
            $session->set('_locale', $requestLocale);
        }

        $locale = $session->get('_locale', $this->defaultLocale);
        if (!in_array($locale, $this->locales, true)) {
            $locale = $this->defaultLocale;
        }

        $request->setLocale($locale);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 20],
        ];
    }
}
