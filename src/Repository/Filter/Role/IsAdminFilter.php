<?php

namespace App\Repository\Filter\Role;

use App\Repository\Filter\AbstractFilter;
use App\Repository\Filter\ComparisonOperator;
use Doctrine\ORM\QueryBuilder;

/**
 * Restricts results to roles with (or without) admin privileges.
 */
class IsAdminFilter extends AbstractFilter
{
    public function __construct(
        private readonly bool $isAdmin = true,
        private readonly ComparisonOperator $operator = ComparisonOperator::EQ,
    ) {
        $this->assertOperator($this->operator, $this->allowedBooleanOperators());
    }

    public function apply(QueryBuilder $qb): void
    {
        $alias = $this->getRootAlias($qb);
        $this->applyComparison($qb, "$alias.isAdmin", 'isAdmin', $this->isAdmin, $this->operator);
    }
}
