<?php

require __DIR__ . '/vendor/autoload.php';

use Scanner\Service;

/**
 * @var $unit SplFileInfo
 */

$root = '../../Development/autoplius';
$scanner = new Service\RenameDirectoryIterator($root);
$filter = new Service\RenameIteratorFilter($scanner);
$iterator = new Service\RenameIterator($filter);

$paths = array($root);
foreach ($iterator as $path => $unit) {
    if (!$unit->isDir()) {
        if ($unit->getExtension() != 'php') {
            continue;
        }

        var_dump($unit->getRealPath());
//        var_dump($unit->isReadable());
//        var_dump($unit->isWritable());
    }
}