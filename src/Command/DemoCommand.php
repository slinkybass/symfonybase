<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Enables or disables the optional demo entity/CRUD/form by swapping tracked files with `docs/Demo/*.phps` backups, then runs `doctrine:schema:update --force` and `app:update-permissions`.
 */
#[AsCommand(name: 'app:demo')]
class DemoCommand extends Command
{
    public function __construct(
        private readonly ParameterBagInterface $params,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $projectDir = $this->params->get('kernel.project_dir');
        $fs = new Filesystem();

        $docsPath = $projectDir.'/docs/Demo';
        $srcPath = $projectDir.'/src';
        $map = [
            $docsPath.'/DemoEntity.phps' => $srcPath.'/Entity/DemoEntity.php',
            $docsPath.'/DemoEntityCrudController.phps' => $srcPath.'/Controller/Admin/Cruds/DemoEntityCrudController.php',
            $docsPath.'/DemoEntityType.phps' => $srcPath.'/Form/Type/DemoEntityType.php',
        ];

        $enabled = !$fs->exists($docsPath.'/DemoEntity.phps');

        $indicator = new ProgressIndicator($output);
        $indicator->start(($enabled ? 'Disabling' : 'Enabling').' demo...');

        // Move files
        foreach ($map as $disabledPath => $enabledPath) {
            $from = $enabled ? $enabledPath : $disabledPath;
            $to = $enabled ? $disabledPath : $enabledPath;
            if (!$fs->exists($from)) {
                $io->error("$from doesn't exist.");

                return Command::FAILURE;
            }
            $fs->rename($from, $to, true);
        }

        // Update schema
        $process = new Process(['php', $projectDir.'/bin/console', 'doctrine:schema:update', '--force']);
        $process->setTimeout(300)->run();
        if (!$process->isSuccessful()) {
            $io->error('Schema update failed.');

            return Command::FAILURE;
        }

        // Update permissions
        $process = new Process(['php', $projectDir.'/bin/console', 'app:update-permissions']);
        $process->setTimeout(300)->run();
        if (!$process->isSuccessful()) {
            $io->error('Permissions update failed.');

            return Command::FAILURE;
        }

        $indicator->finish('Demo is currently '.($enabled ? 'disabled' : 'enabled').'.');

        return Command::SUCCESS;
    }
}
