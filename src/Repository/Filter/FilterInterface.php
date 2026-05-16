<?php

namespace App\Repository\Filter;

use Doctrine\ORM\QueryBuilder;

/**
 * Composable criterion applied to an existing `QueryBuilder` (typically from `AbstractRepository::createQueryBuilder()`).
 */
interface FilterInterface
{
    /**
     * Adds `WHERE`/`JOIN`/`ORDER BY` fragments; multiple filters on the same builder stack with AND semantics.
     */
    public function apply(QueryBuilder $qb): void;
}
