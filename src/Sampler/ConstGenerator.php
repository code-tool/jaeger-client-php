<?php
declare(strict_types=1);

namespace Jaeger\Sampler;

class ConstGenerator implements GeneratorInterface
{
    public function generate(int $traceId, string $operationName): string
    {
        return 'const';
    }
}
