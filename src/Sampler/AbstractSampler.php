<?php

namespace Jaeger\Sampler;

use Jaeger\Tag\DebugRequestTag;

abstract class AbstractSampler implements SamplerInterface
{
    /**
     * @param $tracerId
     * @param $operationName
     *
     * @return SamplerResult
     */
    abstract public function doDecide($tracerId, $operationName);

    /**
     * @param int    $traceId
     * @param string $operationName
     * @param string $debugId
     *
     * @return SamplerResult
     */
    public function decide($traceId, $operationName, $debugId)
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
