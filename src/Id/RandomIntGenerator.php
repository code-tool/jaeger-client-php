<?php

namespace CodeTool\OpenTracing\Id;

class RandomIntGenerator implements IdGeneratorInterface
{
    /**
     * @return int
     */
    public function next()
    {
        return random_int(0, PHP_INT_MAX);
    }
}
