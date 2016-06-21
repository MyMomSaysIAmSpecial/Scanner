<?php

namespace Scanner\Service;

class Iterator extends \RecursiveIteratorIterator
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