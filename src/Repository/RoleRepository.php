<?php

namespace App\Repository;

use App\Entity\Role;
use App\Service\RolePermissions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Role>
 */
class RoleRepository extends ServiceEntityRepository
{
    private $rolePermissions;

    public function __construct(ManagerRegistry $registry, RolePermissions $rolePermissions)
    {
        parent::__construct($registry, Role::class);
        $this->rolePermissions = $rolePermissions;
    }

    /**
     * Retrieves a Role entity by its name.
     *
     * @param string $name the role name
     *
     * @return Role|null the matching role or null if not found
     */
    public function get(string $name): ?Role
    {
        return $this->createQueryBuilder('entity')
            ->where('entity.name = :name')
            ->setParameter('name', $name)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * Retrieves all admin roles based on the isAdmin flag.
     *
     * @param bool $isAdmin whether to fetch admin roles (default: true)
     *
     * @return mixed an array of Role entities
     */
    public function getAdmin(bool $isAdmin = true): mixed
    {
        return $this->getAdminQB($isAdmin)
            ->getQuery()->getResult();
    }

    /**
     * Returns a QueryBuilder instance to find all admin roles based on the isAdmin flag.
     *
     * @param bool $isAdmin whether to fetch admin roles (default: true)
     *
     * @return QueryBuilder a Doctrine QueryBuilder of Role entities
     */
    public function getAdminQB(bool $isAdmin = true): QueryBuilder
    {
        return $this->getAdminSentence(
            $this->createQueryBuilder('entity'), $isAdmin
        );
    }

    /**
     * Returns a QueryBuilder instance to find all admin roles based on the isAdmin flag.
     *
     * @param QueryBuilder $qb the base query builder
     * @param bool $isAdmin whether to fetch admin roles (default: true)
     *
     * @return QueryBuilder a Doctrine QueryBuilder of Role entities
     */
    public function getAdminSentence(QueryBuilder $qb, bool $isAdmin = true): QueryBuilder
    {
        return $qb
            ->where('entity.isAdmin = :isAdmin')
            ->setParameter('isAdmin', $isAdmin)
            ->orderBy('entity.displayName', 'ASC');
    }

    /**
     * Retrieves all admin roles that are "higher" than the given role.
     *
     * @param Role $role the role to compare against
     *
     * @return mixed an array of Role entities considered higher
     */
    public function getAdminIsUp(Role $role): mixed
    {
        return $this->getAdminIsUpQB($role)
            ->getQuery()->getResult();
    }

    /**
     * Returns a QueryBuilder instance to find all admin roles that are "higher" than the given role.
     *
     * @param Role $role the role to compare against
     *
     * @return QueryBuilder a Doctrine QueryBuilder of Role entities considered higher
     */
    public function getAdminIsUpQB(Role $role): QueryBuilder
    {
        return $this->getAdminIsUpSentence(
            $this->createQueryBuilder('entity'), $role
        );
    }

    /**
     * Returns a QueryBuilder instance to find all admin roles that are "higher" than the given role.
     *
     * @param QueryBuilder $qb the base query builder
     * @param Role $role the role to compare against
     *
     * @return QueryBuilder a Doctrine QueryBuilder of Role entities considered higher
     */
    public function getAdminIsUpSentence(QueryBuilder $qb, Role $role): QueryBuilder
    {
        return $qb
            ->andWhere('entity.id IN (' . implode(',', $this->getAdminIsUpIds($role)) . ')')
            ->orderBy('entity.displayName', 'ASC');
    }

    /**
     * Gets an array of IDs of admin roles that are higher than the given role.
     *
     * @param Role $role the role to compare against
     *
     * @return int[] an array of role IDs
     */
    public function getAdminIsUpIds(Role $role): array
    {
        $adminRoles = $this->getAdmin();
        $adminRolesIds = [];
        foreach ($adminRoles as $adminRole) {
            if ($this->rolePermissions->isUp($role, $adminRole)) {
                $adminRolesIds[] = $adminRole->getId();
            }
        }
        return $adminRolesIds;
    }
}
