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

    public function findByRole(string $roleName): mixed
    {
        return $this->findByRoleQB($roleName)
            ->getQuery()->getResult();
    }

    public function findByRoleQB(string $roleName): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.role', 'r')
            ->where('u.verified = true')
            ->andWhere('r.name = :roleName')
            ->setParameter('roleName', $roleName);
    }

    public function findAdmins(bool $isAdmin = true): mixed
    {
        return $this->findAdminsQB($isAdmin)
            ->getQuery()->getResult();
    }

    public function findAdminsQB(bool $isAdmin = true): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.role', 'r')
            ->where('u.verified = true')
            ->andWhere('r.isAdmin = :isAdmin')
            ->setParameter('isAdmin', $isAdmin);
    }
}
