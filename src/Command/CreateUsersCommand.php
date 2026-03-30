<?php

namespace App\Command;

use App\Entity\Role;
use App\Entity\User;
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

#[AsCommand(name: 'app:create-users')]
class CreateUsersCommand extends Command
{
    public const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_USER = 'ROLE_USER';

    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private RolePermissions $rolePermissions)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
        $this->rolePermissions = $rolePermissions;

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

        $roleSuperAdmin = $roleRepo->get(self::ROLE_SUPERADMIN);
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
            $output->writeln('<bg=green;options=bold>CREATED '.self::ROLE_SUPERADMIN.' </>');
        }

        $roleAdmin = $roleRepo->get(self::ROLE_ADMIN);
        if (!$roleAdmin) {
            $roleAdmin = new Role();
            $roleAdmin->setDisplayName('Admin');
            $roleAdmin->setName(self::ROLE_ADMIN);
            $roleAdmin->setIsAdmin(true);
            $this->em->persist($roleAdmin);
            $output->writeln('<bg=green;options=bold>CREATED '.self::ROLE_ADMIN.'</>');
        }

        $roleUser = $roleRepo->get(self::ROLE_USER);
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
            $roleSuperAdmin = $roleRepo->get(self::ROLE_SUPERADMIN);

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
            $roleAdmin = $roleRepo->get(self::ROLE_ADMIN);

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
