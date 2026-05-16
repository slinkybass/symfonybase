<?php

namespace App\Command;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\Filter\Role as RoleFilter;
use App\Repository\Filter\User as UserFilter;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Service\RolePermissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Idempotently seeds default `Role` rows (superadmin with full permission tree, admin, user) and initial superadmin/admin `User` accounts when none exist for those roles.
 *
 * Intended for local/bootstrap setups; change default passwords immediately outside trusted environments.
 */
#[AsCommand(name: 'app:create-users')]
class CreateUsersCommand extends Command
{
    /** Role that receives the full scanned permission tree in this command. */
    public const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';
    /** Admin role name persisted on `Role.name`. */
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    /** Default non-admin role name persisted on `Role.name`. */
    public const ROLE_USER = 'ROLE_USER';

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly RolePermissions $rolePermissions,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->roles($output);
        $this->users($output);

        return Command::SUCCESS;
    }

    private function roles(OutputInterface $output): void
    {
        /** @var RoleRepository $roleRepo */
        $roleRepo = $this->em->getRepository(Role::class);

        $roleSuperAdmin = $roleRepo->filterOne([new RoleFilter\NameFilter(self::ROLE_SUPERADMIN)]);
        if (!$roleSuperAdmin) {
            $roleSuperAdmin = new Role();
            $roleSuperAdmin->setName(self::ROLE_SUPERADMIN);
            $roleSuperAdmin->setDisplayName('Superadmin');
            $roleSuperAdmin->setIsAdmin(true);
            $permissions = $this->rolePermissions->getGroupedPermissions();
            $permissionsValues = [];
            $this->rolePermissions->loopPermissions($permissions, function ($permission) use (&$permissionsValues) {
                $permissionsValues[$permission] = true;
            });
            $roleSuperAdmin->setPermissions($permissionsValues);
            $this->em->persist($roleSuperAdmin);
            $output->writeln('<bg=green;options=bold>CREATED '.self::ROLE_SUPERADMIN.'</>');
        }

        $roleAdmin = $roleRepo->filterOne([new RoleFilter\NameFilter(self::ROLE_ADMIN)]);
        if (!$roleAdmin) {
            $roleAdmin = new Role();
            $roleAdmin->setDisplayName('Admin');
            $roleAdmin->setName(self::ROLE_ADMIN);
            $roleAdmin->setIsAdmin(true);
            $this->em->persist($roleAdmin);
            $output->writeln('<bg=green;options=bold>CREATED '.self::ROLE_ADMIN.'</>');
        }

        $roleUser = $roleRepo->filterOne([new RoleFilter\NameFilter(self::ROLE_USER)]);
        if (!$roleUser) {
            $roleUser = new Role();
            $roleUser->setDisplayName('User');
            $roleUser->setName(self::ROLE_USER);
            $roleUser->setIsAdmin(false);
            $this->em->persist($roleUser);
            $output->writeln('<bg=green;options=bold>CREATED '.self::ROLE_USER.'</>');
        }

        $this->em->flush();
    }

    private function users(OutputInterface $output): void
    {
        /** @var UserRepository $userRepo */
        $userRepo = $this->em->getRepository(User::class);
        /** @var RoleRepository $roleRepo */
        $roleRepo = $this->em->getRepository(Role::class);

        $superAdmins = $userRepo->filter([
            new UserFilter\RoleFilter(self::ROLE_SUPERADMIN),
        ]);
        if (empty($superAdmins)) {
            $roleSuperAdmin = $roleRepo->filterOne([new RoleFilter\NameFilter(self::ROLE_SUPERADMIN)]);

            $superAdmin = new User();
            $superAdmin->setName('Superadmin');
            $superAdmin->setEmail('superadmin@superadmin.com');
            $superAdmin->setRole($roleSuperAdmin);
            $superAdmin->setPassword($this->passwordHasher->hashPassword($superAdmin, 'superadmin'));
            $this->em->persist($superAdmin);
            $output->writeln('<bg=green;options=bold>CREATED USER superadmin@superadmin.com</>');
        }

        $admins = $userRepo->filter([
            new UserFilter\RoleFilter(self::ROLE_ADMIN),
        ]);
        if (empty($admins)) {
            $roleAdmin = $roleRepo->filterOne([new RoleFilter\NameFilter(self::ROLE_ADMIN)]);

            $admin = new User();
            $admin->setName('Admin');
            $admin->setEmail('admin@admin.com');
            $admin->setRole($roleAdmin);
            $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
            $this->em->persist($admin);
            $output->writeln('<bg=green;options=bold>CREATED USER admin@admin.com</>');
        }

        $this->em->flush();
    }
}
