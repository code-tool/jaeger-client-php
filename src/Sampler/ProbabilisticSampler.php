<?php

namespace Jaeger\Sampler;

class ProbabilisticSampler extends AbstractSampler
{
    private $rate;

    private $threshold;

    /**
     * ProbabilisticSampler constructor.
     *
     * @param float $rate
     */
    public function __construct($rate)
    {
        $this->rate = $rate;
        $this->threshold = $rate * PHP_INT_MAX;
    }

    /**
     * @param int    $tracerId
     * @param string $operationName
     *
     * @return SamplerResult
     */
    public function doDecide($tracerId, $operationName)
    {
        if (abs($tracerId) > $this->threshold) {
            return new SamplerResult(
                false,
                0x00,
                [
                    new SamplerTypeTag('probabilistic'),
                    new SamplerParamTag((string)$this->rate),
                    new SamplerDecisionTag(false),
                    new SamplerFlagsTag(0x00),
                ]
            );
        }

        return new SamplerResult(
            true,
            0x01,
            [
                new SamplerTypeTag('probabilistic'),
                new SamplerDecisionTag(true),
                new SamplerFlagsTag(0x01),
                new SamplerParamTag((string)$this->rate)
            ]
        );
    }
}
