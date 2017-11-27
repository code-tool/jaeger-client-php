<?php
declare(strict_types=1);

namespace Jaeger\Sampler;

class ProbabilisticSampler implements SamplerInterface
{
    private $rate;

    private $threshold;

    public function __construct(float $rate)
    {
        $this->rate = $rate;
        $this->threshold = $rate * PHP_INT_MAX;
    }

    public function decide(int $traceId, string $operationName): SamplerResult
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
