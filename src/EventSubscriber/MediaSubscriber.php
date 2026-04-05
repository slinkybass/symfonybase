<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Ensures the media directories required by the file manager exist before handling requests.
 */
class MediaSubscriber implements EventSubscriberInterface
{
    private readonly array $conf;

    public function __construct(
        private readonly Filesystem $filesystem,
        array $artgrisFileManager,
    ) {
        $this->conf = $artgrisFileManager['conf'];
    }

    /**
     * Creates the configured media directory if it does not exist.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->get('_route');
        $conf = $request->get('conf');

        if ($route !== 'file_manager' || !$conf || !array_key_exists($conf, $this->conf)) {
            return;
        }

        $dir = $this->conf[$conf]['dir'];

        if (!$this->filesystem->exists($dir)) {
            $this->filesystem->mkdir($dir);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 30],
        ];
    }
}
