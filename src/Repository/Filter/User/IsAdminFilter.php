<?php

namespace App\Repository\Filter\User;

use App\Repository\Filter\AbstractFilter;
use App\Repository\Filter\ComparisonOperator;
use Doctrine\ORM\QueryBuilder;

/**
 * Restricts results to users with (or without) an admin role.
 */
class IsAdminFilter extends AbstractFilter
{
    public function __construct(
        private readonly bool $isAdmin = true,
        private readonly ComparisonOperator $operator = ComparisonOperator::EQ,
    ) {
        $this->assertOperator($this->operator, [ComparisonOperator::EQ, ComparisonOperator::NEQ]);
    }

    public function apply(QueryBuilder $qb): void
    {
        $this->ensureJoin($qb, 'role', 'r');
        $this->applyComparison($qb, 'r.isAdmin', 'isAdmin', $this->isAdmin, $this->operator);
    }
}
