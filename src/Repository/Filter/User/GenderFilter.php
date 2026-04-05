<?php

namespace App\Repository\Filter\User;

use App\Entity\Enum\UserGender;
use App\Repository\Filter\AbstractFilter;
use App\Repository\Filter\ComparisonOperator;
use Doctrine\ORM\QueryBuilder;

/**
 * Restricts results to users with the given gender.
 */
class GenderFilter extends AbstractFilter
{
    /** @var int[] */
    private array $values;

    /**
     * @param UserGender|int|array<UserGender|int>|null $gender
     */
    public function __construct(
        UserGender|int|array|null $gender,
        private readonly ComparisonOperator $operator = ComparisonOperator::EQ,
    ) {
        $this->assertOperator($this->operator, [
            ComparisonOperator::EQ,
            ComparisonOperator::NEQ,
            ...$this->allowedCollectionOperators(),
            ...$this->allowedNullOperators(),
        ]);

        $this->values = $this->resolveArray(
            $gender ?? [],
            static fn (UserGender|int $g) => $g instanceof UserGender ? $g->value : $g,
        );
    }

    public function apply(QueryBuilder $qb): void
    {
        $alias = $this->getRootAlias($qb);
        $this->applyMultiComparison($qb, "$alias.gender", 'gender', $this->values, $this->operator);
    }
}
