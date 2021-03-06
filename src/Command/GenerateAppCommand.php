<?php

namespace Dealroadshow\Bundle\K8SBundle\Command;

use Dealroadshow\Bundle\K8SBundle\CodeGeneration\AppGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class GenerateAppCommand extends Command
{
    use ClearCacheTrait;

    private const ARGUMENT_PROJECT_NAME = 'project-name';
    private const ARGUMENT_APP_NAME     = 'app-name';

    protected static $defaultName = 'dealroadshow_k8s:generate:app';
    private AppGenerator $generator;

    public function __construct(AppGenerator $generator)
    {
        $this->generator = $generator;
        parent::__construct(null);
    }

    public function configure()
    {
        $this
            ->setDescription('Creates a new K8S App skeleton')
            ->addArgument(
                self::ARGUMENT_PROJECT_NAME,
                InputArgument::REQUIRED,
                'Project name without "project" suffix (e.g. <fg=yellow>k8s-is-awesome</>)'
            )
            ->addArgument(
                self::ARGUMENT_APP_NAME,
                InputArgument::REQUIRED,
                'App name without "app" suffix (e.g. <fg=yellow>cron-jobs</>)'
            )
            ->setAliases([
                'k8s:generate:app',
                'k8s:gen:app',
            ])
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $projectName = $input->getArgument(self::ARGUMENT_PROJECT_NAME);
        $appName = $input->getArgument(self::ARGUMENT_APP_NAME);

        try {
            $fileName = $this->generator->generate($projectName, $appName);
            $this->clearCache();
        } catch (Throwable $e) {
            $io->error($e->getMessage());
            $io->newLine();

            return 1;
        }

        $io->success(
            sprintf('App "%s" successfully generated, see file "%s"', $appName, $fileName)
        );
        $io->newLine();

        return 0;
    }
}
