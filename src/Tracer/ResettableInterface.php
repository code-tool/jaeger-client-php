<?php
declare(strict_types=1);

namespace Jaeger\Tracer;

interface ResettableInterface
{
    public function reset(): ResettableInterface;
}
