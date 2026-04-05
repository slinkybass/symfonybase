<?php

namespace App\EventListener;

use App\Entity\Config;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Invalidates the global cached config so it is rebuilt on next request.
 */
#[AsEntityListener(event: Events::postUpdate, entity: Config::class)]
#[AsEntityListener(event: Events::postPersist, entity: Config::class)]
class ConfigCacheListener
{
    public function __construct(
        private readonly CacheInterface $cache,
    ) {
    }

    /**
     * Invalidates the cached application configuration.
     */
    public function postUpdate(Config $config): void
    {
        $this->invalidate();
    }

    /**
     * Invalidates the cached application configuration.
     */
    public function postPersist(Config $config): void
    {
        $this->invalidate();
    }

    /**
     * Deletes the cached config entry so it is rebuilt on the next request.
     */
    private function invalidate(): void
    {
        $this->cache->delete('app_config');
    }
}
