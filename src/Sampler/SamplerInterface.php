<?php
declare(strict_types=1);

namespace Jaeger\Sampler;

interface SamplerInterface
{
    public function decide(int $traceId, string $operationName, bool $isDebug) : SamplerResult;
}
