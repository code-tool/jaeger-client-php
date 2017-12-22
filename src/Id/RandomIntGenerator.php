<?php

namespace Jaeger\Id;

class RandomIntGenerator implements IdGeneratorInterface
{
    /**
     * @return int
     */
    public function next()
    {
        return random_int(-PHP_INT_MAX + 1, PHP_INT_MAX);
    }
}
