<?php

namespace Scanner\Service;

class RenameIteratorFilter extends \RecursiveFilterIterator
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
