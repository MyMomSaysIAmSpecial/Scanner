<?php

namespace Scanner\Service;

class IteratorFilter extends \RecursiveFilterIterator
{
    private $filters = [];

    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    public function accept()
    {
        return !in_array(
            $this->current()->getFilename(),
            $this->filters,
            true
        );
    }
}
