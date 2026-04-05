<?php

namespace App\Command;

use App\Entity\Role;
use App\Repository\Filter\Role as RoleFilter;
use App\Repository\RoleRepository;
use App\Service\RolePermissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:update-permissions')]
class UpdatePermissionsCommand extends Command
{
    private EntityManagerInterface $em;
    private RolePermissions $rolePermissions;

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
        /** @var RoleRepository $roleRepo */
        $roleRepo = $this->em->getRepository(Role::class);

        $roleSuperAdmin = $roleRepo->filterOne([new RoleFilter\NameFilter(CreateUsersCommand::ROLE_SUPERADMIN)]);
        if ($roleSuperAdmin) {
            $permissions = $this->rolePermissions->getGroupedPermissions();
            $permissionsValues = [];
            $this->rolePermissions->loopPermissions($permissions, function ($permission) use (&$permissionsValues) {
                $permissionsValues[$permission] = true;
            });
            $roleSuperAdmin->setPermissions($permissionsValues);
            $this->em->persist($roleSuperAdmin);
            $output->writeln('<bg=green;options=bold>UPDATED '.CreateUsersCommand::ROLE_SUPERADMIN.'</>');
        }

        $this->em->flush();
    }
}
