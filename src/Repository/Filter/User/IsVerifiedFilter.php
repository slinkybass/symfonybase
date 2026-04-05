<?php

namespace App\Repository\Filter\User;

use App\Repository\Filter\AbstractFilter;
use App\Repository\Filter\ComparisonOperator;
use Doctrine\ORM\QueryBuilder;

/**
 * Restricts results to verified or unverified users.
 */
class IsVerifiedFilter extends AbstractFilter
{
    public function __construct(
        private readonly bool $isVerified = true,
        private readonly ComparisonOperator $operator = ComparisonOperator::EQ,
    ) {
        $this->assertOperator($this->operator, [ComparisonOperator::EQ, ComparisonOperator::NEQ]);
    }

    public function apply(QueryBuilder $qb): void
    {
        $alias = $this->getRootAlias($qb);
        $this->applyComparison($qb, "$alias.verified", 'isVerified', $this->isVerified, $this->operator);
    }
}
