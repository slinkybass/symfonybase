<?php

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * For Artgris `file_manager` requests, ensures the upload directory for the selected `conf` query key exists on disk.
 */
final class MediaSubscriber
{
    private readonly array $conf;

    public function __construct(
        private readonly Filesystem $filesystem,
        #[Autowire('%artgris_file_manager%')]
        array $artgrisFileManager,
    ) {
        $this->conf = $artgrisFileManager['conf'];
    }

    /**
     * No-op unless `_route` is `file_manager`, `conf` is present, and it matches a key in the injected Artgris config map.
     */
    #[AsEventListener(priority: 30)]
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        $conf = $request->query->get('conf');

        if ($route !== 'file_manager' || !$conf || !array_key_exists($conf, $this->conf)) {
            return;
        }

        $dir = $this->conf[$conf]['dir'];

        if (!$this->filesystem->exists($dir)) {
            $this->filesystem->mkdir($dir);
        }
    }
}
