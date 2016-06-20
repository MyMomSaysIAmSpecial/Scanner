<?php

class RenameDirectoryIterator extends RecursiveDirectoryIterator
{
    public function __construct($root)
    {
        parent::__construct(
            $root,
            RecursiveDirectoryIterator::SKIP_DOTS
        );
    }
}

class RenameIteratorFilter extends RecursiveFilterIterator
{
    private $filters = [
        '.idea',
        '.vendor',
        'app',
        'node_modules',
        'coverage',
        'documents',
        'vendor',
    ];

    public function accept()
    {
        return !in_array(
            $this->current()->getFilename(),
            $this->filters,
            true
        );
    }
}

class RenameIterator extends RecursiveIteratorIterator
{
    public function __construct(FilterIterator $filter)
    {
        parent::__construct(
            $filter,
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );
    }
}

/**
 * @var $unit SplFileInfo
 */

$root = '../../Development/autoplius';
$scanner = new RenameDirectoryIterator($root);
$filter = new RenameIteratorFilter($scanner);
$iterator = new RenameIterator($filter);

$paths = array($root);
foreach ($iterator as $path => $unit) {
    if (!$unit->isDir()) {
        if ($unit->getExtension() != 'php') {
            continue;
        }

//        var_dump($unit->getRealPath());
//        var_dump($unit->isReadable());
//        var_dump($unit->isWritable());
    }
}