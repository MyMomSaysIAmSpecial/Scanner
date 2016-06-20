<?php

namespace Scanner\Service;

class RenameIterator extends \RecursiveIteratorIterator
{
    public function __construct(\FilterIterator $filter)
    {
        parent::__construct(
            $filter,
            \RecursiveIteratorIterator::SELF_FIRST,
            \RecursiveIteratorIterator::CATCH_GET_CHILD
        );
    }
}