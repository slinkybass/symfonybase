<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * 
     * @param PasswordAuthenticatedUserInterface $user
     * @param string $newHashedPassword
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Generates a random password.
     * 
     * @param int $length
     * @param bool $mayus
     * @param bool $minus
     * @param bool $numbers
     * @param bool $symbols
     * 
     * @return string
     */
    public function generatePassword($length = 8)
    {
        $uppercase = "ABCDEFGHJKLMNPQRSTUVWXYZ";
        $lowercase = "abcdefghijkmnpqrstuvwxyz";
        $numbers = "23456789";
        $specials = "!@#$%&_";
        $all = $uppercase . $lowercase . $numbers . $specials;

        $pick = function($str, $count = 1) {
            $result = '';
            $max = strlen($str) - 1;
            for ($i = 0; $i < $count; $i++) {
                $result .= $str[mt_rand(0, $max)];
            }
            return $result;
        };

        $password = '';
        $password .= $pick($specials, 1);
        $password .= $pick($lowercase, 1);
        $password .= $pick($uppercase, 1);
        $password .= $pick($numbers, 1);

        if ($length > 4) {
            $password .= $pick($all, $length - 4);
        }

        $passwordArray = str_split($password);
        shuffle($passwordArray);
        return implode('', $passwordArray);
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
        return $this->findByRoleSentence(
            $this->createQueryBuilder('entity'), $roleName
        );
    }

    /**
     * Returns a QueryBuilder instance to find all users who belong to a given role.
     *
     * @param QueryBuilder $qb       the base query builder
     * @param string       $roleName the name of the role
     *
     * @return QueryBuilder a Doctrine QueryBuilder of User entities matching the role
     */
    public function findByRoleSentence(QueryBuilder $qb, string $roleName): QueryBuilder
    {
        return $qb
            ->innerJoin('entity.role', 'r')
            ->where('entity.verified = true')
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
        return $this->findAdminsSentence(
            $this->createQueryBuilder('entity'), $isAdmin
        );
    }

    /**
     * Returns a QueryBuilder instance to find all users who have (or don't have) an admin role.
     *
     * @param QueryBuilder $qb      the base query builder
     * @param bool         $isAdmin whether to filter for admin roles (default: true)
     *
     * @return QueryBuilder a Doctrine QueryBuilder of User entities with the specified admin status
     */
    public function findAdminsSentence(QueryBuilder $qb, bool $isAdmin = true): QueryBuilder
    {
        return $qb
            ->leftJoin('entity.role', 'r')
            ->where('entity.verified = true')
            ->andWhere('r.isAdmin = :isAdmin')
            ->setParameter('isAdmin', $isAdmin);
    }
}
