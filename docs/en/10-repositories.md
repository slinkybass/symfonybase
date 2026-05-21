# Repositories and filters

## `App\Repository\AbstractRepository`

`src/Repository/AbstractRepository.php`. Extends `Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository` and provides a uniform query API based on **filter objects**:

```php
$users = $userRepo->filter([new User\IsActiveFilter(), new User\NameFilter('Lia')]);
$user  = $userRepo->filterOne([new User\EmailFilter($email)]);
$first = $userRepo->filterFirst([new User\IsAdminFilter()]);
$count = $userRepo->filterCount();
$exists = $userRepo->filterExists([new User\EmailFilter($email)]);
$page  = $userRepo->filterPaginated($filters, page: 2, limit: 25);
$qb    = $userRepo->applyFilters($filters); // raw QueryBuilder for further chaining
```

A subclass sets `protected static string $alias` to choose the DQL alias for its root entity. Filters always operate on this alias.

## `App\Repository\Filter\FilterInterface`

`src/Repository/Filter/FilterInterface.php`. Single method:

```php
public function apply(QueryBuilder $qb): void;
```

Multiple filters stack with `AND` semantics on the same builder. They may add `WHERE`, `JOIN`, `ORDER BY` clauses.

## `App\Repository\Filter\AbstractFilter`

`src/Repository/Filter/AbstractFilter.php`. Shared helpers:

- `getRootAlias($qb)` — returns the first root alias.
- `ensureJoin($qb, $relation, $joinAlias)` — idempotent `leftJoin(root.relation, alias)`.
- `applyComparison($qb, $field, $param, $value, ComparisonOperator $op)` — switch on every supported operator (LIKE, NOT_LIKE, STARTS_WITH, ENDS_WITH, EQ, NEQ, IN, NOT_IN, GT, GTE, LT, LTE, BETWEEN, IS_NULL, IS_NOT_NULL).
- `applyMultiComparison($qb, $field, $param, $values, $op)` — auto-promotes `EQ` → `IN` (and `NEQ` → `NOT_IN`) depending on the count of values.
- `assertOperator($op, $allowed)` — throws on disallowed operators.
- `allowedStringOperators()`, `allowedBooleanOperators()`, `allowedNumericOperators()`, `allowedCollectionOperators()`, `allowedNullOperators()`, `allowedDateOperators()`.
- `parseDate($date)` / `resolveDates($value)` — accept either `\DateTimeInterface` or strings in common formats (`Y-m-d`, `d/m/Y`, ISO 8601, with optional time components).
- `resolveArray($value, $resolver)` — normalizes to a unique array.

## Operators and ordering

`App\Repository\Filter\ComparisonOperator` (enum) lists every supported comparison; `BETWEEN` expects a 2-element array, `IN`/`NOT_IN` expect an array of scalars, `IS_NULL`/`IS_NOT_NULL` ignore the value.

`App\Repository\Filter\OrderDirection` (`ASC` / `DESC`) drives `App\Repository\Filter\OrderFilter`:

```php
$repo->filter([new OrderFilter('createdAt', OrderDirection::DESC)]);
$repo->filter([new OrderFilter('name', alias: 'r')]); // when joined under alias r
```

## Per-entity filters

`src/Repository/Filter/User/`:

- `EmailFilter` (string operators).
- `NameFilter`, `LastnameFilter`, `FullnameFilter`.
- `BirthdateFilter` (date + null operators).
- `GenderFilter` (uses `UserGender` enum).
- `IsActiveFilter`, `IsVerifiedFilter`, `IsAdminFilter` (joins on `role`).
- `RoleFilter` — accepts `string`, `Role`, or arrays; normalizes to `ROLE_*` and matches against the joined `role.name`.

`src/Repository/Filter/Role/`:

- `IsAdminFilter` — boolean comparison on `role.isAdmin`.
- `NameFilter` — string comparison.

Constructors accept the value first and an optional `ComparisonOperator` (default `EQ` or `LIKE` per filter). Each filter asserts the operator against its allowed set up front so misuse fails fast.

## Repositories

`src/Repository/`:

- `UserRepository` — extends `AbstractRepository<User>`, alias `u`, implements `PasswordUpgraderInterface` so Symfony can transparently rehash passwords.
- `RoleRepository` — alias `r`.
- `ConfigRepository` — alias `c`; primarily used by [`ConfigService`](03-configuration.md).
- `ResetPasswordRequestRepository` — implements `ResetPasswordRequestRepositoryInterface` (SymfonyCasts) via the bundle trait; intentionally does **not** extend `AbstractRepository`.

## Authoring a new filter

1. Subclass `AbstractFilter`.
2. Inject the value(s) and an optional `ComparisonOperator`.
3. Call `assertOperator($this->operator, $this->allowedXxxOperators())` from the constructor.
4. In `apply()`: derive the alias, `ensureJoin()` if you target a related entity, then call `applyComparison()` / `applyMultiComparison()`.
5. If the filter is reused across entities, place it under `src/Repository/Filter/<Entity>/`.

Avoid building ad-hoc queries inside controllers when a repository + filter combination already exists for the same case.
