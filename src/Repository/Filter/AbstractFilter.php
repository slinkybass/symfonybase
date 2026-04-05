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

    /** @return ComparisonOperator[] */
    protected function allowedStringOperators(): array
    {
        return [
            ComparisonOperator::EQ,
            ComparisonOperator::NEQ,
            ComparisonOperator::LIKE,
            ComparisonOperator::NOT_LIKE,
            ComparisonOperator::STARTS_WITH,
            ComparisonOperator::ENDS_WITH,
        ];
    }

    /** @return ComparisonOperator[] */
    protected function allowedBooleanOperators(): array
    {
        return [
            ComparisonOperator::EQ,
            ComparisonOperator::NEQ,
        ];
    }

    /** @return ComparisonOperator[] */
    protected function allowedNumericOperators(): array
    {
        return [
            ComparisonOperator::EQ,
            ComparisonOperator::NEQ,
            ComparisonOperator::GT,
            ComparisonOperator::GTE,
            ComparisonOperator::LT,
            ComparisonOperator::LTE,
            ComparisonOperator::BETWEEN,
        ];
    }

    /** @return ComparisonOperator[] */
    protected function allowedCollectionOperators(): array
    {
        return [
            ComparisonOperator::IN,
            ComparisonOperator::NOT_IN,
        ];
    }

    /** @return ComparisonOperator[] */
    protected function allowedNullOperators(): array
    {
        return [
            ComparisonOperator::IS_NULL,
            ComparisonOperator::IS_NOT_NULL,
        ];
    }

    /** @return ComparisonOperator[] */
    protected function allowedDateOperators(): array
    {
        return $this->allowedNumericOperators();
    }

    /**
     * Parses a string or returns a DateTimeInterface instance as-is.
     *
     * @throws \InvalidArgumentException if the string cannot be parsed as a valid date or datetime
     */
    protected function parseDate(\DateTimeInterface|string $date): \DateTimeImmutable
    {
        if ($date instanceof \DateTimeImmutable) {
            return $date;
        }

        if ($date instanceof \DateTime) {
            return \DateTimeImmutable::createFromMutable($date);
        }

        $formats = [
            // Date only
            'Y-m-d',
            'd/m/Y',
            'd-m-Y',
            // Date + hours:minutes
            'Y-m-d H:i',
            'd/m/Y H:i',
            'd-m-Y H:i',
            // Date + hours:minutes:seconds
            'Y-m-d H:i:s',
            'd/m/Y H:i:s',
            'd-m-Y H:i:s',
            // Date + T (ISO 8601)
            'Y-m-d\TH:i',
            'Y-m-d\TH:i:s',
        ];

        foreach ($formats as $format) {
            $parsed = \DateTimeImmutable::createFromFormat($format, $date);
            if ($parsed !== false) {
                return $parsed;
            }
        }

        throw new \InvalidArgumentException(sprintf('Invalid date string "%s". Expected formats: %s.', $date, implode(', ', $formats)));
    }

    /**
     * Resolves a date value to a DateTimeImmutable instance, an array of DateTimeImmutable instances, or null.
     *
     * @param \DateTimeInterface|string|array<\DateTimeInterface|string>|null $value
     *
     * @return \DateTimeImmutable|\DateTimeImmutable[]|null
     */
    protected function resolveDates(\DateTimeInterface|string|array|null $value): \DateTimeImmutable|array|null
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return array_map(fn (\DateTimeInterface|string $date) => $this->parseDate($date), $value);
        }

        return $this->parseDate($value);
    }
}
