<?php

namespace App\Repository;

use App\Repository\Filter\FilterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Base repository providing filter-based querying for all entities.
 *
 * @template T of object
 *
 * @extends ServiceEntityRepository<T>
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    protected static string $alias = 'entity';

    /**
     * Returns all results matching the given filters.
     *
     * @param FilterInterface|FilterInterface[] $filters
     *
     * @return array<int, object>
     */
    public function filter(FilterInterface|array $filters = []): array
    {
        return $this->applyFilters($filters)
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns a single result matching the given filters, or null if not found.
     *
     * @param FilterInterface|FilterInterface[] $filters
     */
    public function filterOne(FilterInterface|array $filters = []): ?object
    {
        return $this->applyFilters($filters)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Returns the first result matching the given filters, or null if not found.
     *
     * @param FilterInterface|FilterInterface[] $filters
     */
    public function filterFirst(FilterInterface|array $filters = []): ?object
    {
        return $this->applyFilters($filters)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Returns the number of results matching the given filters.
     *
     * @param FilterInterface|FilterInterface[] $filters
     */
    public function filterCount(FilterInterface|array $filters = []): int
    {
        return (int) $this->applyFilters($filters)
            ->select('COUNT('.static::$alias.'.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Returns whether at least one result matches the given filters.
     *
     * @param FilterInterface|FilterInterface[] $filters
     */
    public function filterExists(FilterInterface|array $filters = []): bool
    {
        return $this->filterFirst($filters) !== null;
    }

    /**
     * Returns a paginated set of results matching the given filters.
     *
     * @param FilterInterface|FilterInterface[] $filters
     *
     * @return array<int, object>
     */
    public function filterPaginated(FilterInterface|array $filters = [], int $page = 1, int $limit = 10): array
    {
        return $this->applyFilters($filters)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns a QueryBuilder with the given filters applied.
     *
     * @param FilterInterface|FilterInterface[] $filters
     */
    public function applyFilters(FilterInterface|array $filters = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder(static::$alias);

        foreach ((array) $filters as $filter) {
            $filter->apply($qb);
        }

        return $qb;
    }
}
