<?php

namespace Jaeger\Sampler;

class ConstSampler extends AbstractSampler
{
    private $debugEnabled;

    public function __construct($debugEnabled)
    {
        $this->debugEnabled = (bool)$debugEnabled;
    }

    /**
     * @param int    $tracerId
     * @param string $operationName
     *
     * @return SamplerResult
     */
    public function doDecide($tracerId, $operationName)
    {
        if (false === $this->debugEnabled) {
            return new SamplerResult(
                false,
                0,
                [
                    new SamplerTypeTag('const'),
                    new SamplerParamTag('False'),
                    new SamplerDecisionTag(false),
                    new SamplerFlagsTag(0x00),
                ]
            );
        }

        return new SamplerResult(
            true,
            0x01,
            [
                new SamplerTypeTag('const'),
                new SamplerParamTag('True'),
                new SamplerDecisionTag(true),
                new SamplerFlagsTag(0x01),
            ]
        );
    }
}
