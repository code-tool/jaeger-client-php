<?php
declare(strict_types=1);

namespace Jaeger\Sampler;

class OperationGenerator implements GeneratorInterface
{
    public function generate(int $traceId, string $operationName): string
    {
        return 'operation:' . $operationName;
    }
}
