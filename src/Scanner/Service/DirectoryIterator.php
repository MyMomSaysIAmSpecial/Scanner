<?php

namespace Scanner\Service;

class DirectoryIterator extends \RecursiveDirectoryIterator
{
    public function __construct($root)
    {
        parent::__construct(
            $root,
            \RecursiveDirectoryIterator::SKIP_DOTS
        );
    }
}
