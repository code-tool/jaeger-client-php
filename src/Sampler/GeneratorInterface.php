<?php
declare(strict_types=1);

namespace Jaeger\Sampler;

interface GeneratorInterface
{
    public function generate(int $traceId, string $operationName) : string;
}
