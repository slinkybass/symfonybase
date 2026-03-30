<?php

namespace App\Repository\Filter;

use Doctrine\ORM\QueryBuilder;

/**
 * Base class for QueryBuilder filters with join deduplication support.
 */
abstract class AbstractFilter implements FilterInterface
{
    /**
     * Adds a join to the QueryBuilder only if it has not already been applied.
     *
     * @param string $alias     the root alias to join from
     * @param string $relation  the relation name on the root entity
     * @param string $joinAlias the alias to assign to the joined entity
     */
    protected function ensureJoin(QueryBuilder $qb, string $alias, string $relation, string $joinAlias): void
    {
        foreach ($qb->getDQLPart('join') as $joins) {
            foreach ($joins as $join) {
                if ($join->getAlias() === $joinAlias) {
                    return;
                }
            }
        }

        $qb->leftJoin("$alias.$relation", $joinAlias);
    }
}
