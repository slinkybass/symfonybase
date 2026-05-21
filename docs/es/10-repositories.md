# Repositorios y filtros

## `App\Repository\AbstractRepository`

`src/Repository/AbstractRepository.php`. Extiende `Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository` y proporciona una API de consulta uniforme basada en **objetos filtro**:

```php
$users = $userRepo->filter([new User\IsActiveFilter(), new User\NameFilter('Lia')]);
$user  = $userRepo->filterOne([new User\EmailFilter($email)]);
$first = $userRepo->filterFirst([new User\IsAdminFilter()]);
$count = $userRepo->filterCount();
$exists = $userRepo->filterExists([new User\EmailFilter($email)]);
$page  = $userRepo->filterPaginated($filters, page: 2, limit: 25);
$qb    = $userRepo->applyFilters($filters); // QueryBuilder sin procesar para encadenar más operaciones
```

Una subclase define `protected static string $alias` para elegir el alias DQL de su entidad raíz. Los filtros siempre operan sobre este alias.

## `App\Repository\Filter\FilterInterface`

`src/Repository/Filter/FilterInterface.php`. Método único:

```php
public function apply(QueryBuilder $qb): void;
```

Varios filtros se apilan con semántica `AND` sobre el mismo builder. Pueden añadir cláusulas `WHERE`, `JOIN` y `ORDER BY`.

## `App\Repository\Filter\AbstractFilter`

`src/Repository/Filter/AbstractFilter.php`. Helpers compartidos:

- `getRootAlias($qb)` — devuelve el primer alias raíz.
- `ensureJoin($qb, $relation, $joinAlias)` — `leftJoin(root.relation, alias)` idempotente.
- `applyComparison($qb, $field, $param, $value, ComparisonOperator $op)` — ramifica por cada operador soportado (LIKE, NOT_LIKE, STARTS_WITH, ENDS_WITH, EQ, NEQ, IN, NOT_IN, GT, GTE, LT, LTE, BETWEEN, IS_NULL, IS_NOT_NULL).
- `applyMultiComparison($qb, $field, $param, $values, $op)` — promueve automáticamente `EQ` → `IN` (y `NEQ` → `NOT_IN`) según la cantidad de valores.
- `assertOperator($op, $allowed)` — lanza una excepción con operadores no permitidos.
- `allowedStringOperators()`, `allowedBooleanOperators()`, `allowedNumericOperators()`, `allowedCollectionOperators()`, `allowedNullOperators()`, `allowedDateOperators()`.
- `parseDate($date)` / `resolveDates($value)` — aceptan tanto `\DateTimeInterface` como cadenas en formatos habituales (`Y-m-d`, `d/m/Y`, ISO 8601, con componentes de hora opcionales).
- `resolveArray($value, $resolver)` — normaliza a un array único.

## Operadores y ordenación

`App\Repository\Filter\ComparisonOperator` (enum) lista todas las comparaciones soportadas; `BETWEEN` espera un array de 2 elementos, `IN`/`NOT_IN` esperan un array de escalares, e `IS_NULL`/`IS_NOT_NULL` ignoran el valor.

`App\Repository\Filter\OrderDirection` (`ASC` / `DESC`) controla `App\Repository\Filter\OrderFilter`:

```php
$repo->filter([new OrderFilter('createdAt', OrderDirection::DESC)]);
$repo->filter([new OrderFilter('name', alias: 'r')]); // cuando se une bajo el alias r
```

## Filtros por entidad

`src/Repository/Filter/User/`:

- `EmailFilter` (operadores de cadena).
- `NameFilter`, `LastnameFilter`, `FullnameFilter`.
- `BirthdateFilter` (operadores de fecha + nulo).
- `GenderFilter` (usa el enum `UserGender`).
- `IsActiveFilter`, `IsVerifiedFilter`, `IsAdminFilter` (hace join en `role`).
- `RoleFilter` — acepta `string`, `Role` o arrays; normaliza a `ROLE_*` y compara contra el `role.name` del join.

`src/Repository/Filter/Role/`:

- `IsAdminFilter` — comparación booleana sobre `role.isAdmin`.
- `NameFilter` — comparación de cadena.

Los constructores aceptan el valor primero y un `ComparisonOperator` opcional (por defecto `EQ` o `LIKE` según el filtro). Cada filtro comprueba el operador contra su conjunto permitido de entrada, de modo que un uso incorrecto falla de inmediato.

## Repositorios

`src/Repository/`:

- `UserRepository` — extiende `AbstractRepository<User>`, alias `u`, implementa `PasswordUpgraderInterface` para que Symfony pueda rehaschear contraseñas de forma transparente.
- `RoleRepository` — alias `r`.
- `ConfigRepository` — alias `c`; utilizado principalmente por [`ConfigService`](03-configuration.md).
- `ResetPasswordRequestRepository` — implementa `ResetPasswordRequestRepositoryInterface` (SymfonyCasts) mediante el trait del bundle; intencionadamente **no** extiende `AbstractRepository`.

## Cómo crear un nuevo filtro

1. Subclasifica `AbstractFilter`.
2. Inyecta el/los valor(es) y un `ComparisonOperator` opcional.
3. Llama a `assertOperator($this->operator, $this->allowedXxxOperators())` desde el constructor.
4. En `apply()`: deriva el alias, usa `ensureJoin()` si apuntas a una entidad relacionada, y llama a `applyComparison()` / `applyMultiComparison()`.
5. Si el filtro se reutiliza entre entidades, colócalo en `src/Repository/Filter/<Entity>/`.

Evita construir consultas ad-hoc en los controladores cuando ya existe una combinación repositorio + filtro para el mismo caso.
