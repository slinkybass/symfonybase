<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Create the media directory if it doesn't exist.
 */
class MediaSubscriber implements EventSubscriberInterface
{
    private array $artgrisFileManager;

    public function __construct(array $artgrisFileManager)
    {
        $this->artgrisFileManager = $artgrisFileManager['conf'];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if ($request->get('_route') == "file_manager" && $request->get('conf') && array_key_exists($request->get('conf'), $this->artgrisFileManager)) {
            $config = $this->artgrisFileManager[$request->get('conf')];
            $filesystem = new Filesystem();
            if (!$filesystem->exists($config['dir'])) {
                $filesystem->mkdir($config['dir']);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
