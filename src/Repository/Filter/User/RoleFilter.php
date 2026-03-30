<?php

namespace App\Repository\Filter\User;

use App\Repository\Filter\AbstractFilter;
use App\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Restricts results to users belonging to the given role.
 */
class RoleFilter extends AbstractFilter
{
    public function __construct(private readonly string $roleName)
    {
    }

    public function apply(QueryBuilder $qb): void
    {
        $this->ensureJoin($qb, UserRepository::$alias, 'role', 'r');
        $qb
            ->andWhere('r.name = :roleName')
            ->setParameter('roleName', $this->roleName);
    }
}
