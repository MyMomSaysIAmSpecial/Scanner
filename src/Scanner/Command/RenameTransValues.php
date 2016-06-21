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

        $lost = $continue = [];
        foreach ($keys as $key) {
            $found = 0;
            $io->progressAdvance(1);
            $io->text('Searching for ' . $formatter->truncate($key, 75));
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

                            /**
                             * Long translation keys are crashing regexp,
                             * and short one must be checked with function tags
                             *
                             * Ex.: It's not enought for "eur" just to check is it exist in a content, because
                             * it can be just a currency typehint without translation, or a constant (EUR), on the
                             * other hand, long key is most likely a token
                             */
                            if (strlen($key) > 30) {
                                $check = strpos($content, $key);
                            } else {
                                $key = preg_quote($key);
                                $check = preg_match("#__\(['|\"]({$key})['|\"]\)#", $content);
                            }

                            if ($check) {
                                $found = 1;

                                $content = preg_replace("#__\(['|\"]({$key})['|\"]\)#", '__(\'magic_shit\')', $content);
                                $file->ftruncate($file->getSize());
                                $file->fwrite($content);
//
                                $io->newLine();
                                $io->note('Found ' . $key .  ' in ' . $unit->getRealPath());
                                $continue = $io->choice('Continue?', [1 => 'Yes', 'No'], 'Yes');

                                if ($continue == 'No') {
                                    break;
                                }
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