<?php

namespace App\EventListener;

use App\Entity\Config;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Invalidates the global cached config so it is rebuilt on next request.
 */
#[AsEntityListener(event: Events::postUpdate, entity: Config::class, method: 'invalidate')]
#[AsEntityListener(event: Events::postPersist, entity: Config::class, method: 'invalidate')]
final class ConfigCacheListener
{
    public function __construct(
        private readonly CacheInterface $cache,
    ) {
    }

    /**
     * Deletes the cached config entry so it is rebuilt on the next request.
     */
    public function invalidate(Config $config): void
    {
        $this->cache->delete('app_config');
    }
}
