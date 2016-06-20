<?php

namespace Scanner\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Scanner\Service;

class RenameTransValues extends Command
{
    protected function configure()
    {
        $this
            ->setName('rename_values')
            ->setDescription('Greet someone')
            ->addArgument(
                'root',
                InputArgument::REQUIRED,
                'Please set project root'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var $unit \SplFileInfo
         */

        $root = $input->getArgument('root');
        $scanner = new Service\RenameDirectoryIterator($root);
        $filter = new Service\RenameIteratorFilter($scanner);
        $iterator = new Service\RenameIterator($filter);

        foreach ($iterator as $path => $unit) {
            if (!$unit->isDir()) {
                if ($unit->getExtension() != 'php') {
                    continue;
                }

                $output->writeln('<info>' . $unit->getRealPath() . '</info>');
            }
        }
    }
}