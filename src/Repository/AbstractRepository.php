<?php

namespace App\Repository;

use App\Repository\Filter\FilterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Base `ServiceEntityRepository` that composes `FilterInterface` instances as additional `AND` conditions.
 *
 * Subclasses set `static::$alias` for DQL; filters receive the same `QueryBuilder` from `createQueryBuilder()`.
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
     * @return list<T>
     */
    public function filter(FilterInterface|array $filters = []): array
    {
        return $this->applyFilters($filters)
            ->getQuery()
            ->getResult();
    }

    /**
     * Exactly one entity or null; not constrained to unique filters — multiple rows throw.
     *
     * @param FilterInterface|FilterInterface[] $filters
     *
     * @return T|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException when more than one row matches
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
     *
     * @return T|null
     */
    public function filterFirst(FilterInterface|array $filters = []): ?object
    {
        return $this->applyFilters($filters)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
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
     * Whether any row matches; implemented as `filterFirst()` with `LIMIT 1` (not `COUNT()`).
     *
     * @param FilterInterface|FilterInterface[] $filters
     */
    public function filterExists(FilterInterface|array $filters = []): bool
    {
        return $this->filterFirst($filters) !== null;
    }

    /**
     * @param FilterInterface|FilterInterface[] $filters
     * @param int                                 $page  1-based page number
     * @param int                                 $limit max rows per page
     *
     * @return list<T>
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
