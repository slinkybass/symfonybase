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
}
