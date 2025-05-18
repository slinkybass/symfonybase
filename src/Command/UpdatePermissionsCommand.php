<?php

namespace App\Command;

use App\Entity\Role;
use App\Service\RolePermissions;
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
    private $rolePermissions;

    public function __construct(EntityManagerInterface $em, RolePermissions $rolePermissions)
    {
        $this->em = $em;
        $this->rolePermissions = $rolePermissions;

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
            $crudPermissions = $this->rolePermissions->getCrudPermissions();
            $crudPermissionsValues = [];
            $this->rolePermissions->loopPermissions($crudPermissions, function ($permission) use (&$crudPermissionsValues) {
                $crudPermissionsValues[$permission] = true;
            });
            $roleSuperAdmin->setPermissions($crudPermissionsValues);
            $this->em->persist($roleSuperAdmin);
            $output->writeln('<bg=green;options=bold>UPDATED ROLE_SUPERADMIN</>');
        }

        $this->em->flush();
    }
}
