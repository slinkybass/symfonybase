<?php

namespace App\Repository\Filter\User;

use App\Repository\Filter\AbstractFilter;
use App\Repository\Filter\ComparisonOperator;
use Doctrine\ORM\QueryBuilder;

/**
 * Restricts results to users with the given fullname.
 */
class FullnameFilter extends AbstractFilter
{
    public function __construct(
        private readonly string $fullname,
        private readonly ComparisonOperator $operator = ComparisonOperator::LIKE,
    ) {
        $this->assertOperator($this->operator, $this->allowedStringOperators());
    }

    public function apply(QueryBuilder $qb): void
    {
        $alias = $this->getRootAlias($qb);
        $field = $qb->expr()->concat($alias.'.name', $qb->expr()->literal(' '), $alias.'.lastname');
        $this->applyComparison($qb, (string) $field, 'fullname', $this->fullname, $this->operator);
    }
}
