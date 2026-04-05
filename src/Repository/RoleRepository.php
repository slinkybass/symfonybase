<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Role>
 */
class RoleRepository extends AbstractRepository
{
    protected static string $alias = 'r';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }
}
