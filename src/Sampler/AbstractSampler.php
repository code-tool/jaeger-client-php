<?php
declare(strict_types=1);

namespace Jaeger\Sampler;

use Jaeger\Tag\DebugRequestTag;

abstract class AbstractSampler implements SamplerInterface
{
    abstract public function doDecide(int $tracerId, string $operationName): SamplerResult;

    public function decide(int $traceId, string $operationName, string $debugId): SamplerResult
    {
        if ('' === $debugId) {
            return $this->doDecide($traceId, $operationName);
        }

        return new SamplerResult(
            true,
            0x03,
            [
                new SamplerDecisionTag(true),
                new SamplerTypeTag('debug'),
                new DebugRequestTag($debugId),
                new SamplerFlagsTag(0x03),
                new SamplingPriorityTag(1)
            ]
        );
    }
}
