<?php
declare(strict_types=1);

namespace Jaeger\Sampler;

abstract class AbstractSampler implements SamplerInterface
{
    abstract public function doDecide(int $tracerId, string $operationName): SamplerResult;

    public function decide(int $traceId, string $operationName, bool $isDebug): SamplerResult
    {
        if (false === $isDebug) {
            return $this->doDecide($traceId, $operationName);
        }

        return new SamplerResult(
            true,
            0x03,
            [new SamplerTypeTag('debug'), new SamplingPriorityTag(1)]
        );
    }
}
