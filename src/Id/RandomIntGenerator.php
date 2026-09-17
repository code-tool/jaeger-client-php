<?php

declare(strict_types=1);

namespace Jaeger\Id;

use Exception;

class RandomIntGenerator implements IdGeneratorInterface
{
    public function next(): int
    {
        try {
            return random_int(PHP_INT_MIN, PHP_INT_MAX);
        } catch (Exception) {
        } finally {
            return random_int(PHP_INT_MIN, PHP_INT_MAX);
        }
    }
}
