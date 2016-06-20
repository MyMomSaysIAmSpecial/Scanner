<?php

namespace Scanner\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Scanner\Service;
use Symfony\Component\Console\Style\SymfonyStyle;

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
        $io = new SymfonyStyle($input, $output);


        /**
         * @var $unit \SplFileInfo
         * @var $formatter FormatterHelper
         */

        $root = $input->getArgument('root');

        $scanner = new Service\RenameDirectoryIterator($root);
        $filter = new Service\RenameIteratorFilter($scanner);
        $iterator = new Service\RenameIterator($filter);

        try {
            $translationsPath = $root . 'app/autoplius/translation/messages.en.php';

            if (!file_exists($translationsPath)) {
                throw new \Exception;
            }

            $translations = require_once $translationsPath;
            $keys = array_keys($translations);

            if (empty($keys)) {
                throw new \Exception;
            }

            $io->success(['Translations file found. Loaded ' .count($keys) . ' keys']);
        } catch(\Exception $e) {
            $io->error(['Translation file not found or is empty.']);
            exit;
        }

        $io->note('Process started');

        foreach ($iterator as $path => $unit) {
            if (!$unit->isDir()) {
                if ($unit->getExtension() != 'php') {
                    continue;
                }

                $output->writeln($unit->getRealPath());
                if ($unit->isWritable()) {
                    $file = $unit->openFile('a+');
                    $content = $file->fread($file->getSize());
                    if (strpos($content, '__(') !== false) {
                        $output->writeln(strpos($content, '__('));
                        break;
                    }
//                    $file->fwrite("appended this sample text");
                }
            }
        }
    }
}