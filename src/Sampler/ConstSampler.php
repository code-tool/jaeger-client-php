<?php
declare(strict_types=1);

namespace Jaeger\Sampler;

class ConstSampler implements SamplerInterface
{
    const SAMPLER_TYPE_CONST = '';

    private $debugEnabled;

    public function __construct(bool $debugEnabled)
    {
        $this->debugEnabled = $debugEnabled;
    }

    public function decide(int $traceId, string $operationName): SamplerResult
    {
        return new SamplerResult(
            $this->debugEnabled,
            [
                new SamplerTypeTag('const'),
                new SamplerParamTag($this->debugEnabled ? 'True' : 'False')
            ]
        );
    }
}
