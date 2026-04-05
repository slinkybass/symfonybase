<?php

namespace App\EventListener;

use App\Entity\Config;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Invalidates the session config cache when the Config entity is updated.
 */
#[AsEntityListener(event: Events::postUpdate, entity: Config::class)]
#[AsEntityListener(event: Events::postPersist, entity: Config::class)]
class ConfigCacheListener
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * Removes the cached config from the session so it is rebuilt on the next request.
     */
    public function postUpdate(Config $config): void
    {
        $this->invalidate();
    }

    /**
     * Removes the cached config from the session so it is rebuilt on the next request.
     */
    public function postPersist(Config $config): void
    {
        $this->invalidate();
    }

    private function invalidate(): void
    {
        $this->requestStack->getSession()->remove('config');
    }
}
