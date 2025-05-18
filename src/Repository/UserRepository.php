<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Retrieves all users who belong to a given role.
     *
     * @param string $roleName the name of the role
     *
     * @return mixed an array of User entities matching the role
     */
    public function findByRole(string $roleName): mixed
    {
        return $this->findByRoleQB($roleName)
            ->getQuery()->getResult();
    }

    /**
     * Returns a QueryBuilder instance to find all users who belong to a given role.
     *
     * @param string $roleName the name of the role
     *
     * @return QueryBuilder a Doctrine QueryBuilder of User entities matching the role
     */
    public function findByRoleQB(string $roleName): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.role', 'r')
            ->where('u.verified = true')
            ->andWhere('r.name = :roleName')
            ->setParameter('roleName', $roleName);
    }

    /**
     * Retrieves all users who have (or don't have) an admin role.
     *
     * @param bool $isAdmin whether to filter by admin roles (default: true)
     *
     * @return mixed an array of User entities with the specified admin status
     */
    public function findAdmins(bool $isAdmin = true): mixed
    {
        return $this->findAdminsQB($isAdmin)
            ->getQuery()->getResult();
    }

    /**
     * Returns a QueryBuilder instance to find all users who have (or don't have) an admin role.
     *
     * @param bool $isAdmin whether to filter for admin roles (default: true)
     *
     * @return QueryBuilder a Doctrine QueryBuilder of User entities with the specified admin status
     */
    public function findAdminsQB(bool $isAdmin = true): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.role', 'r')
            ->where('u.verified = true')
            ->andWhere('r.isAdmin = :isAdmin')
            ->setParameter('isAdmin', $isAdmin);
    }
}
