<?php

namespace Jaeger\Id;

class RandomIntGenerator implements IdGeneratorInterface
{
    /**
     * @return int
     */
    public function next()
    {
        return rand(PHP_INT_MIN, PHP_INT_MAX);
    }
}
