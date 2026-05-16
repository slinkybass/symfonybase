<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Resolves `Request::setLocale()` from session `_locale`, optionally primed by `_locale` in route attributes or query.
 *
 * `$locales` is a pipe-separated allow-list from configuration (priority 40, main request only).
 */
final class LocaleSubscriber implements EventSubscriberInterface
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
     * Requires an active session; invalid stored locales fall back to the configured default.
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
