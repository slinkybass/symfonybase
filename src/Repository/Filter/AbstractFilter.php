<?php

namespace App\Repository\Filter;

use Doctrine\ORM\QueryBuilder;

/**
 * Base class for QueryBuilder filters with join deduplication support.
 */
abstract class AbstractFilter implements FilterInterface
{
    /**
     * Returns the root alias of the given QueryBuilder.
     */
    protected function getRootAlias(QueryBuilder $qb): string
    {
        return $qb->getRootAliases()[0];
    }

    /**
     * Adds a join to the QueryBuilder only if it has not already been applied.
     *
     * @param string $relation  the relation name on the root entity
     * @param string $joinAlias the alias to assign to the joined entity
     */
    protected function ensureJoin(QueryBuilder $qb, string $relation, string $joinAlias): void
    {
        foreach ($qb->getDQLPart('join') as $joins) {
            foreach ($joins as $join) {
                if ($join->getAlias() === $joinAlias) {
                    return;
                }
            }
        }

        $qb->leftJoin($this->getRootAlias($qb).".$relation", $joinAlias);
    }

    /**
     * Applies a comparison expression to the QueryBuilder.
     *
     * @param string $field     the field expression to compare
     * @param string $paramName the parameter name to bind
     * @param mixed  $value     the value to compare against (for BETWEEN, expects array{0: mixed, 1: mixed})
     */
    protected function applyComparison(
        QueryBuilder $qb,
        string $field,
        string $paramName,
        mixed $value,
        ComparisonOperator $operator = ComparisonOperator::LIKE,
    ): void {
        match ($operator) {
            ComparisonOperator::LIKE => $qb->andWhere($qb->expr()->like($field, ":$paramName"))->setParameter($paramName, '%'.$value.'%'),
            ComparisonOperator::NOT_LIKE => $qb->andWhere($qb->expr()->notLike($field, ":$paramName"))->setParameter($paramName, '%'.$value.'%'),
            ComparisonOperator::STARTS_WITH => $qb->andWhere($qb->expr()->like($field, ":$paramName"))->setParameter($paramName, $value.'%'),
            ComparisonOperator::ENDS_WITH => $qb->andWhere($qb->expr()->like($field, ":$paramName"))->setParameter($paramName, '%'.$value),
            ComparisonOperator::EQ => $qb->andWhere($qb->expr()->eq($field, ":$paramName"))->setParameter($paramName, $value),
            ComparisonOperator::NEQ => $qb->andWhere($qb->expr()->neq($field, ":$paramName"))->setParameter($paramName, $value),
            ComparisonOperator::IN => $qb->andWhere($qb->expr()->in($field, ":$paramName"))->setParameter($paramName, $value),
            ComparisonOperator::NOT_IN => $qb->andWhere($qb->expr()->notIn($field, ":$paramName"))->setParameter($paramName, $value),
            ComparisonOperator::GT => $qb->andWhere($qb->expr()->gt($field, ":$paramName"))->setParameter($paramName, $value),
            ComparisonOperator::GTE => $qb->andWhere($qb->expr()->gte($field, ":$paramName"))->setParameter($paramName, $value),
            ComparisonOperator::LT => $qb->andWhere($qb->expr()->lt($field, ":$paramName"))->setParameter($paramName, $value),
            ComparisonOperator::LTE => $qb->andWhere($qb->expr()->lte($field, ":$paramName"))->setParameter($paramName, $value),
            ComparisonOperator::BETWEEN => $qb->andWhere($qb->expr()->between($field, ":{$paramName}From", ":{$paramName}To"))->setParameter("{$paramName}From", $value[0])->setParameter("{$paramName}To", $value[1]),
            ComparisonOperator::IS_NULL => $qb->andWhere($qb->expr()->isNull($field)),
            ComparisonOperator::IS_NOT_NULL => $qb->andWhere($qb->expr()->isNotNull($field)),
        };
    }

    /**
     * Validates that the given operator is among the allowed ones for this filter.
     *
     * @param ComparisonOperator[] $allowed
     *
     * @throws \InvalidArgumentException
     */
    protected function assertOperator(ComparisonOperator $operator, array $allowed): void
    {
        if (!in_array($operator, $allowed, true)) {
            throw new \InvalidArgumentException(sprintf('%s does not support the "%s" operator. Allowed: %s.', static::class, $operator->name, implode(', ', array_map(fn (ComparisonOperator $o) => $o->name, $allowed))));
        }
    }
}
