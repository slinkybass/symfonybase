<?php

namespace App\Repository\Filter\User;

use App\Repository\Filter\AbstractFilter;
use App\Repository\Filter\ComparisonOperator;
use Doctrine\ORM\QueryBuilder;

/**
 * Restricts results to users with the given name.
 */
class NameFilter extends AbstractFilter
{
    public function __construct(
        private readonly string $name,
        private readonly ComparisonOperator $operator = ComparisonOperator::LIKE,
    ) {
        $this->assertOperator($this->operator, $this->allowedStringOperators());
    }

    public function apply(QueryBuilder $qb): void
    {
        $alias = $this->getRootAlias($qb);
        $this->applyComparison($qb, "$alias.name", 'name', $this->name, $this->operator);
    }
}
