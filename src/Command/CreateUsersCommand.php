<?php

namespace App\Command;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-users')]
class CreateUsersCommand extends Command
{
    private $em;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;

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
        $roleSuperAdmin = $this->em->getRepository(Role::class)->get("ROLE_SUPERADMIN");
        if (!$roleSuperAdmin) {
            $roleSuperAdmin = new Role();
            $roleSuperAdmin->setName('ROLE_SUPERADMIN');
            $roleSuperAdmin->setDisplayName('Superadmin');
            $roleSuperAdmin->setIsAdmin(true);
            $permissions = [];
            $filesInsideCrudsFolder = array_diff(scandir(__DIR__ . '/../Controller/Admin/Cruds'), ['..', '.']);
            foreach ($filesInsideCrudsFolder as $fileName) {
                $crudName = str_replace('CrudController.php', '', $fileName);
                if (!preg_match('/CrudController.php$/', $fileName)) {
                    continue;
                }
                $permName = 'crud' . ucfirst($crudName);
                $permissions[$permName] = true;
                $actionNames = [Action::NEW, Action::DETAIL, Action::EDIT, Action::DELETE];
                $actionNames = $crudName == "Config" ? [Action::EDIT] : $actionNames;
                foreach ($actionNames as $actionName) {
                    $subPermName = 'crud' . $crudName . ucfirst($actionName);
                    $permissions[$subPermName] = true;
                }
            }
            $roleSuperAdmin->setPermissions($permissions);
            $this->em->persist($roleSuperAdmin);
            $output->writeln('<bg=green;options=bold>CREATED ROLE_SUPERADMIN</>');
        }

        $roleAdmin = $this->em->getRepository(Role::class)->get("ROLE_ADMIN");
        if (!$roleAdmin) {
            $roleAdmin = new Role();
            $roleAdmin->setDisplayName('Admin');
            $roleAdmin->setName('ROLE_ADMIN');
            $roleAdmin->setIsAdmin(true);
            $this->em->persist($roleAdmin);
            $output->writeln('<bg=green;options=bold>CREATED ROLE_ADMIN</>');
        }

        $roleUser = $this->em->getRepository(Role::class)->get("ROLE_USER");
        if (!$roleUser) {
            $roleUser = new Role();
            $roleUser->setDisplayName('User');
            $roleUser->setName('ROLE_USER');
            $roleUser->setIsAdmin(false);
            $this->em->persist($roleUser);
            $output->writeln('<bg=green;options=bold>CREATED ROLE_USER</>');
        }

        $this->em->flush();
    }

    private function users(OutputInterface $output): void
    {
        $superAdmins = $this->em->getRepository(User::class)->findByRole("ROLE_SUPERADMIN");
        if (!count($superAdmins)) {
            $roleSuperAdmin = $this->em->getRepository(Role::class)->get("ROLE_SUPERADMIN");

            $superAdmin = new User();
            $superAdmin->setName('Superadmin');
            $superAdmin->setEmail('superadmin@superadmin.com');
            $superAdmin->setRole($roleSuperAdmin);
            $superAdmin->setPassword($this->passwordHasher->hashPassword($superAdmin, 'superadmin'));
            $this->em->persist($superAdmin);
            $output->writeln('<bg=green;options=bold>CREATED USER superadmin@superadmin.com</>');
        }

        $admins = $this->em->getRepository(User::class)->findByRole("ROLE_ADMIN");
        if (!count($admins)) {
            $roleAdmin = $this->em->getRepository(Role::class)->get("ROLE_ADMIN");

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
