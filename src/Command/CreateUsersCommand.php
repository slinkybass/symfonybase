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
		$roleSuperAdminExists = $this->em->getRepository(Role::class)->findOneBy([
			'name' => "ROLE_SUPERADMIN"
		]);
		if (!$roleSuperAdminExists) {
			$roleSuperAdmin = new Role();
			$roleSuperAdmin->setName('ROLE_SUPERADMIN');
			$roleSuperAdmin->setDisplayName('Superadmin');
			$roleSuperAdmin->setIsAdmin(true);
			$permissions = array();
			$filesInsideCrudsFolder = array_diff(scandir(__DIR__ . '/../Controller/Admin/Cruds'), array('..', '.'));
			foreach ($filesInsideCrudsFolder as $fileName) {
				$crudName = str_replace('CrudController.php', '', $fileName);
				if (!preg_match('/CrudController.php$/', $fileName)) { continue; }
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

		$roleAdminExists = $this->em->getRepository(Role::class)->findOneBy([
			'name' => "ROLE_ADMIN"
		]);
		if (!$roleAdminExists) {
			$roleAdmin = new Role();
			$roleAdmin->setDisplayName('Admin');
			$roleAdmin->setName('ROLE_ADMIN');
			$roleAdmin->setIsAdmin(true);
			$this->em->persist($roleAdmin);
			$output->writeln('<bg=green;options=bold>CREATED ROLE_ADMIN</>');
		}

		$roleUserExists = $this->em->getRepository(Role::class)->findOneBy([
			'name' => "ROLE_USER"
		]);
		if (!$roleUserExists) {
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
		$roleSuperAdmin = $this->em->getRepository(Role::class)->findOneBy([
			'name' => "ROLE_SUPERADMIN"
		]);
		$superAdmins = $this->em->getRepository(User::class)->findBy([
			'role' => $roleSuperAdmin
		]);
		if (!count($superAdmins)) {
			$superAdmin = new User();
			$superAdmin->setName('Superadmin');
			$superAdmin->setEmail('superadmin@superadmin.com');
			$superAdmin->setRole($roleSuperAdmin);
			$superAdmin->setPassword($this->passwordHasher->hashPassword($superAdmin, 'superadmin'));
			$output->writeln('<bg=green;options=bold>CREATED USER superadmin@superadmin.com</>');
			$this->em->persist($superAdmin);
			$output->writeln('<bg=green;options=bold>CREATED USER superadmin@superadmin.com</>');
		}

        $roleAdmin = $this->em->getRepository(Role::class)->findOneBy([
            'name' => "ROLE_ADMIN"
        ]);
		$admins = $this->em->getRepository(User::class)->findBy([
			'role' => $roleSuperAdmin
		]);
		if (!count($admins)) {
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