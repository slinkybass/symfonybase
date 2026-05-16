<?php

namespace App\EventListener;

use App\Entity\Config;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Doctrine entity listener on `Config`: clears the `app_config` cache item after persist/update.
 *
 * Must stay in sync with the `ConfigService` cache key so `AppConfig` is rebuilt on the following read.
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
     * @param Config $config entity instance (unused; required by the Doctrine listener callback signature)
     */
    public function invalidate(Config $config): void
    {
        $this->cache->delete('app_config');
    }
}
