<?php
declare(strict_types=1);

namespace Jaeger\Sampler;

class ProbabilisticSampler extends AbstractSampler
{
    private $rate;

    private $threshold;

    public function __construct(float $rate)
    {
        $this->rate = $rate;
        $this->threshold = $rate * PHP_INT_MAX;
    }

    public function doDecide(int $traceId, string $operationName): SamplerResult
    {
        if ($traceId > $this->threshold) {
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
