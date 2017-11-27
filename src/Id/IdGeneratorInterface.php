<?php
declare(strict_types=1);

namespace Jaeger\Id;

interface IdGeneratorInterface
{
    public function next() : int;
}
