<?php

namespace Scanner\Service;

class DirectoryIteratorFactory
{
    public function getDirectoryIterator(Config $config)
    {
        return new DirectoryIterator($config->get('root'));
    }
}
