<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Sampler;

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
            true,
            [
                new SamplerTypeTag('const'),
                new SamplerParamTag($this->debugEnabled ? 'True' : 'False')
            ]
        );
    }
}
