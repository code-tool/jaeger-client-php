<?php
declare(strict_types=1);

namespace Jaeger\Id;

class RandomIntGenerator implements IdGeneratorInterface
{
    public function next(): int
    {
        try {
            return random_int(PHP_INT_MIN, PHP_INT_MAX);
        } catch (\Exception $e) {
        } finally {
            return rand(PHP_INT_MIN, PHP_INT_MAX);
        }
    }
}
