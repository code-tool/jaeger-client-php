<?php
declare(strict_types=1);

namespace Jaeger\Id;

class RandomIntGenerator implements IdGeneratorInterface
{
    public function next(): int
    {
        return random_int(PHP_INT_MIN, PHP_INT_MAX);
    }
}
