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

        $this->roleNames = $this->resolveArray($roles,
            static function (string|Role $role): string {
                $name = $role instanceof Role ? $role->getName() : $role;
                $name = strtoupper($name);

                return str_starts_with($name, 'ROLE_') ? $name : 'ROLE_'.$name;
            },
        );
    }

    public function apply(QueryBuilder $qb): void
    {
        $this->ensureJoin($qb, 'role', 'r');
        $this->applyMultiComparison($qb, 'r.name', 'roleName', $this->roleNames, $this->operator);
    }
}
