<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Sampler;

class ProbabilisticSampler implements SamplerInterface
{
    private $rate;

    public function __construct(float $rate)
    {
        $this->rate = $rate;
    }

    public function decide(int $traceId, string $operationName): SamplerResult
    {
        if ($traceId > $this->rate * PHP_INT_MAX) {
            return new SamplerResult(false);
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
