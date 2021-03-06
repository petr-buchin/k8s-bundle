<?php

namespace Dealroadshow\Bundle\K8SBundle\Command;

use Dealroadshow\Bundle\K8SBundle\CodeGeneration\ProjectGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class GenerateProjectCommand extends Command
{
    use ClearCacheTrait;

    private const ARGUMENT_PROJECT_NAME = 'project-name';

    protected static $defaultName = 'dealroadshow_k8s:generate:project';
    private ProjectGenerator $generator;

    public function __construct(ProjectGenerator $generator)
    {
        $this->generator = $generator;

        parent::__construct(null);
    }

    public function configure()
    {
        $this
            ->setDescription('Creates a new K8S project skeleton')
            ->addArgument(
                self::ARGUMENT_PROJECT_NAME,
                InputArgument::REQUIRED,
                'Project name without "project" suffix (e.g. <fg=yellow>k8s-is-awesome</>)'
            )
            ->setAliases([
                'k8s:generate:project',
                'k8s:gen:project',
            ])
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $projectName = $input->getArgument(self::ARGUMENT_PROJECT_NAME);

        try {
            $fileName = $this->generator->generate($projectName);
            $this->clearCache();
        } catch(Throwable $e) {
            $io->error($e->getMessage());
            $io->newLine();

            return 1;
        }

        $io->success(
            sprintf('Project "%s" successfully generated, see file "%s"', $projectName, $fileName)
        );
        $io->newLine();

        return 0;
    }
}