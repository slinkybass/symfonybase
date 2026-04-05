<?php

namespace App\Repository\Filter\User;

use App\Repository\Filter\AbstractFilter;
use App\Repository\Filter\ComparisonOperator;
use Doctrine\ORM\QueryBuilder;

/**
 * Restricts results to active or inactive users.
 */
class IsActiveFilter extends AbstractFilter
{
    public function __construct(
        private readonly bool $isActive = true,
        private readonly ComparisonOperator $operator = ComparisonOperator::EQ,
    ) {
        $this->assertOperator($this->operator, $this->allowedBooleanOperators());
    }

    public function apply(QueryBuilder $qb): void
    {
        $alias = $this->getRootAlias($qb);
        $this->applyComparison($qb, "$alias.active", 'isActive', $this->isActive, $this->operator);
    }
}
