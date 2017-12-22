<?php

namespace Jaeger\Sampler;

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
     * @param bool   $isDebug
     *
     * @return SamplerResult
     */
    public function decide($traceId, $operationName, $isDebug)
    {
        if (false === $isDebug) {
            return $this->doDecide($traceId, $operationName);
        }

        return new SamplerResult(
            true,
            0x03,
            [
                new SamplerDecisionTag(true),
                new SamplerTypeTag('debug'),
                new SamplerFlagsTag(0x03),
                new SamplingPriorityTag(1)
            ]
        );
    }
}
