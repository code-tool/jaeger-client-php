<?php
declare(strict_types=1);

namespace Jaeger\Id;

class RandomIntGenerator implements IdGeneratorInterface
{
    public function next(): int
    {
        return random_int(0, PHP_INT_MAX);
    }
}
