<?php

namespace App\Repository\Filter;

use Doctrine\ORM\QueryBuilder;

/**
 * Applies an ORDER BY clause to the QueryBuilder.
 */
class OrderFilter extends AbstractFilter
{
    /**
     * @param string         $field     the field name to order by, relative to the root alias
     * @param OrderDirection $direction the sort direction
     * @param string|null    $alias     optional join alias to use instead of the root alias (e.g. 'r' for a joined role)
     */
    public function __construct(
        private readonly string $field,
        private readonly OrderDirection $direction = OrderDirection::ASC,
        private readonly ?string $alias = null,
    ) {
    }

    public function apply(QueryBuilder $qb): void
    {
        $alias = $this->alias ?? $this->getRootAlias($qb);
        $qb->addOrderBy("$alias.$this->field", $this->direction->value);
    }
}
