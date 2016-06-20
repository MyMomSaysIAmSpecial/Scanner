<?php

require __DIR__ . '/vendor/autoload.php';

use Scanner\Command\RenameTransValues;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new RenameTransValues());
$application->run();
