<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Id;

interface IdGeneratorInterface
{
    public function next() : int;
}
