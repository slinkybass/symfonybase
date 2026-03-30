<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends AbstractRepository<User>
 */
class UserRepository extends AbstractRepository implements PasswordUpgraderInterface
{
    public static string $alias = 'u';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Upgrades (rehashes) the user's password automatically over time.
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
     * Generates a random password guaranteed to contain at least one uppercase letter,
     * one lowercase letter, one number and one special character.
     *
     * @param int $length the total length of the generated password (minimum 4)
     */
    public function generatePassword(int $length = 8): string
    {
        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lowercase = 'abcdefghijkmnpqrstuvwxyz';
        $numbers = '23456789';
        $specials = '!@#$%&_';
        $all = $uppercase.$lowercase.$numbers.$specials;

        $pick = static function (string $str, int $count = 1): string {
            $result = '';
            $max = strlen($str) - 1;
            for ($i = 0; $i < $count; ++$i) {
                $result .= $str[random_int(0, $max)];
            }

            return $result;
        };

        $password = $pick($specials);
        $password .= $pick($lowercase);
        $password .= $pick($uppercase);
        $password .= $pick($numbers);

        if ($length > 4) {
            $password .= $pick($all, $length - 4);
        }

        $passwordArray = str_split($password);
        shuffle($passwordArray);

        return implode('', $passwordArray);
    }
}
