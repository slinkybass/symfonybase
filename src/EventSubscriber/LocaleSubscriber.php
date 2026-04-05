<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Sets the application locale on each request based on session or request parameters.
 */
class LocaleSubscriber implements EventSubscriberInterface
{
    /** @var string[] */
    private readonly array $locales;

    public function __construct(
        private readonly string $defaultLocale,
        string $locales,
    ) {
        $this->locales = explode('|', $locales);
    }

    /**
     * Resolves and applies the locale for the current request.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->hasSession()) {
            return;
        }

        $session = $request->getSession();
        $requestLocale = $request->attributes->get('_locale') ?? $request->query->get('_locale');

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
            KernelEvents::REQUEST => ['onKernelRequest', 40],
        ];
    }
}
