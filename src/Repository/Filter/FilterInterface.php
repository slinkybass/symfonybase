<?php

namespace App\Repository\Filter;

use Doctrine\ORM\QueryBuilder;

/**
 * Interface for all QueryBuilder filters.
 */
interface FilterInterface
{
    public function apply(QueryBuilder $qb): void;
}
