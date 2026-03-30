<?php

namespace App\Repository\Filter\User;

use App\Repository\Filter\AbstractFilter;
use App\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Restricts results to verified users.
 */
class VerifiedFilter extends AbstractFilter
{
    public function apply(QueryBuilder $qb): void
    {
        $qb->andWhere(UserRepository::$alias.'.verified = true');
    }
}
