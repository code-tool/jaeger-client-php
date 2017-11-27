<?php

namespace Jaeger\Sampler;

class ProbabilisticSampler implements SamplerInterface
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
     * @param int    $traceId
     * @param string $operationName
     *
     * @return SamplerResult
     */
    public function decide($traceId, $operationName)
    {
        if ($traceId > $this->threshold) {
            return new SamplerResult(
                false,
                [
                    new SamplerTypeTag('probabilistic'),
                    new SamplerParamTag((string)$this->rate)
                ]
            );
        }

        return new SamplerResult(
            true,
            [
                new SamplerTypeTag('probabilistic'),
                new SamplerParamTag((string)$this->rate)
            ]
        );
    }
}
