<?php

namespace App\Repository\Filter\User;

use App\Entity\Role;
use App\Repository\Filter\AbstractFilter;
use App\Repository\Filter\ComparisonOperator;
use Doctrine\ORM\QueryBuilder;

/**
 * Restricts results to users with a role matching the given criteria.
 */
class RoleFilter extends AbstractFilter
{
    /** @var string[] */
    private array $roleNames;

    /**
     * @param string|Role|array<string|Role> $roles
     */
    public function __construct(
        string|Role|array $roles,
        private readonly ComparisonOperator $operator = ComparisonOperator::EQ,
    ) {
        $this->assertOperator($this->operator, [
            ...$this->allowedCollectionOperators(),
            ComparisonOperator::EQ,
            ComparisonOperator::NEQ,
        ]);

        $this->roleNames = $this->resolve($roles);
    }

    public function apply(QueryBuilder $qb): void
    {
        $this->ensureJoin($qb, 'role', 'r');

        $isSingle = count($this->roleNames) === 1;
        $operator = $isSingle
            ? ($this->operator === ComparisonOperator::NOT_IN ? ComparisonOperator::NEQ : ComparisonOperator::EQ)
            : ($this->operator === ComparisonOperator::NEQ ? ComparisonOperator::NOT_IN : $this->operator);

        $param = $isSingle ? $this->roleNames[0] : $this->roleNames;
        $this->applyComparison($qb, 'r.name', 'roleName', $param, $operator);
    }

    /**
     * Resolves the given roles input to a normalized array of unique role name strings.
     *
     * @param string|Role|array<string|Role> $roles
     *
     * @return string[]
     */
    private function resolve(string|Role|array $roles): array
    {
        $roles = is_array($roles) ? $roles : [$roles];

        $resolved = array_map(static function (string|Role $role): string {
            $name = $role instanceof Role ? $role->getName() : $role;
            $name = strtoupper($name);

            return str_starts_with($name, 'ROLE_') ? $name : 'ROLE_'.$name;
        }, $roles);

        return array_values(array_unique($resolved));
    }
}
