<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Sets the request locale based on the session or the `_locale` parameter.
 */
class LocaleSubscriber implements EventSubscriberInterface
{
    private $defaultLocale;
    private $locales;

    public function __construct(string $defaultLocale, string $locales)
    {
        $this->defaultLocale = $defaultLocale;
        $this->locales = $locales;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }
        $localesStr = explode('|', $this->locales);
        if ($request->get('_locale') && in_array($request->get('_locale'), $localesStr)) {
            $request->getSession()->set('_locale', $request->get('_locale'));
        }
        $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
