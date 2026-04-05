<?php

namespace App\Repository\Filter\User;

use App\Repository\Filter\AbstractFilter;
use App\Repository\Filter\ComparisonOperator;
use Doctrine\ORM\QueryBuilder;

/**
 * Restricts results to users with the given lastname.
 */
class LastnameFilter extends AbstractFilter
{
    public function __construct(
        private readonly string|null $lastname,
        private readonly ComparisonOperator $operator = ComparisonOperator::LIKE,
    ) {
        $this->assertOperator($this->operator, [
            ...$this->allowedStringOperators(),
            ...$this->allowedNullOperators(),
        ]);
    }

    public function apply(QueryBuilder $qb): void
    {
        $alias = $this->getRootAlias($qb);
        $this->applyComparison($qb, "$alias.lastname", 'lastname', $this->lastname, $this->operator);
    }
}
