<?php

namespace App\Repository\Filter\User;

use App\Entity\Role;
use App\Repository\Filter\AbstractFilter;
use Doctrine\ORM\QueryBuilder;

/**
 * Restricts results to users belonging to one or more roles.
 * Accepts a role name, a Role entity, or an array of either.
 */
class RoleFilter extends AbstractFilter
{
    /** @var string[] */
    private array $roleNames;

    /**
     * @param string|Role|array<string|Role> $roles
     */
    public function __construct(string|Role|array $roles)
    {
        $this->roleNames = $this->resolve($roles);
    }

    public function apply(QueryBuilder $qb): void
    {
        $this->ensureJoin($qb, 'role', 'r');

        if (count($this->roleNames) === 1) {
            $qb
                ->andWhere('r.name = :roleName')
                ->setParameter('roleName', $this->roleNames[0]);

            return;
        }

        $qb
            ->andWhere('r.name IN (:roleNames)')
            ->setParameter('roleNames', $this->roleNames);
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
