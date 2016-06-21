<?php

namespace Scanner\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;


class RenameTransValues extends Command
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('rename_values')
            ->setDescription('Greet someone')
            ->addArgument(
                'root',
                InputArgument::REQUIRED,
                'Please set project root'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $root = $input->getArgument('root');

        $config = $this->container->get('config');
        $config->set('root', $root);

        /**
         * @var $unit \SplFileInfo
         * @var $formatter FormatterHelper
         */

        $formatter = $this->getHelperSet()->get('formatter');

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

            $io->success(['Translations file found. Loaded ' . count($keys) . ' keys']);
        } catch (\Exception $e) {
            $io->error(['Translation file not found or is empty.']);
            exit;
        }

        $iterator = $this->container->get('iterator');

        $io->progressStart(count($keys));

        $lost = [];
        foreach ($keys as $key) {
            $found = 0;
            $io->progressAdvance(1);
            $io->write(' Searching for ' . $formatter->truncate($key, 75));
            foreach ($iterator as $path => $unit) {
                if (!$unit->isDir()) {
                    if ($unit->getExtension() != 'php') {
                        continue;
                    }

                    if ($unit->isWritable()) {
                        $file = $unit->openFile('r+');

                        # Can't read empty file
                        if ($file->getSize()) {
                            $content = $file->fread($file->getSize());

                            # strpos is faster than regexp
                            if (strpos($content, $key) !== false) {
                                $found = 1;
                                continue;
////                        $content = preg_replace("#__\(['|\"]?(.*)['|\"]?\)#i", 'magic_shit', $content);
//                            preg_match_all("#__\(['|\"](.*)['|\"]\)#i", $content, $found);
//                            var_dump($found);
////                        $file->fwrite($content);
//
//                            $continue = $io->choice('Continue?', [1 => 'Yes', 'No'], 'Yes');
//
//                            if ($continue == 'No') {
//                                break;
//                            }
                            }
                        }
                    }
                }
            }
            if (!$found) {
                $lost[] = $key;
            }
        }
        $io->progressFinish();
        $io->warning('Translations not found in code: ' . count($lost));
        (new \SplFileInfo(getcwd() . '/log.txt'))->openFile('w+')->fwrite(implode("\n", $lost));

    }
}