<?php

namespace App\Repository\Filter\User;

use App\Repository\Filter\AbstractFilter;
use App\Repository\Filter\ComparisonOperator;
use Doctrine\ORM\QueryBuilder;

/**
 * Restricts results to users with a birthdate matching the given criteria.
 */
class BirthdateFilter extends AbstractFilter
{
    private \DateTimeImmutable|array|null $resolved;

    /**
     * @param \DateTimeInterface|string|array<\DateTimeInterface|string>|null $value a single date or string, a [from, to] array for BETWEEN, or null for IS_NULL/IS_NOT_NULL
     */
    public function __construct(
        \DateTimeInterface|string|array|null $value,
        private readonly ComparisonOperator $operator = ComparisonOperator::EQ,
    ) {
        $this->assertOperator($this->operator, [
            ...$this->allowedDateOperators(),
            ...$this->allowedNullOperators(),
        ]);

        $this->resolved = $this->resolveDates($value);
    }

    public function apply(QueryBuilder $qb): void
    {
        $alias = $this->getRootAlias($qb);
        $this->applyComparison($qb, "$alias.birthdate", 'birthdate', $this->resolved, $this->operator);
    }
}
