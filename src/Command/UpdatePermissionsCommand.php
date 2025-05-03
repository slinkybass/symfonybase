<?php

namespace App\Command;

use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:update-permissions')]
class UpdatePermissionsCommand extends Command
{
	private $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;

		parent::__construct();
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->permissions($output);

		return Command::SUCCESS;
	}

	private function permissions(OutputInterface $output): void
	{
		$roleSuperAdmin = $this->em->getRepository(Role::class)->get("ROLE_SUPERADMIN");
		if ($roleSuperAdmin) {
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
			$output->writeln('<bg=green;options=bold>UPDATED ROLE_SUPERADMIN</>');
		}

		$this->em->flush();
	}
}
