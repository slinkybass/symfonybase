<?php

namespace App\Repository\Filter\User;

use App\Repository\Filter\AbstractFilter;
use Doctrine\ORM\QueryBuilder;

/**
 * Restricts results to verified users.
 */
class VerifiedFilter extends AbstractFilter
{
    public function __construct(private readonly bool $isVerified = true)
    {
    }

    public function apply(QueryBuilder $qb): void
    {
        $qb
            ->andWhere($this->getRootAlias($qb).'.verified = :isVerified')
            ->setParameter('isVerified', $this->isVerified);
    }
}
