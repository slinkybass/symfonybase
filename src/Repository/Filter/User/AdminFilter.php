<?php

namespace App\Repository\Filter\User;

use App\Repository\Filter\AbstractFilter;
use App\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Restricts results to users with (or without) an admin role.
 */
class AdminFilter extends AbstractFilter
{
    public function __construct(private readonly bool $isAdmin = true)
    {
    }

    public function apply(QueryBuilder $qb): void
    {
        $this->ensureJoin($qb, UserRepository::$alias, 'role', 'r');
        $qb
            ->andWhere('r.isAdmin = :isAdmin')
            ->setParameter('isAdmin', $this->isAdmin);
    }
}
