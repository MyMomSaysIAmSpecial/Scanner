<?php

namespace Scanner\Service;

class Config
{
    private $parameters = [];

    public function set($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    public function add($value) {
        $this->parameters = array_merge($this->parameters, $value);
    }

    public function get($key)
    {
        return $this->parameters[$key] ? : false;
    }

    public function getAll()
    {
        return $this->parameters;
    }
}
