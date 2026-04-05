<?php

namespace App\Repository;

use App\Entity\Config;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Config>
 */
class ConfigRepository extends AbstractRepository
{
    protected static string $alias = 'c';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Config::class);
    }
}
