<?php
declare(strict_types=1);

namespace Jaeger\Sampler;

class ConstSampler extends AbstractSampler
{
    private $debugEnabled;

    public function __construct(bool $debugEnabled)
    {
        $this->debugEnabled = $debugEnabled;
    }

    public function doDecide(int $traceId, string $operationName): SamplerResult
    {
        if (false === $this->debugEnabled) {
            return new SamplerResult(
                false,
                0,
                [
                    new SamplerTypeTag('const'),
                    new SamplerParamTag('False')
                ]
            );
        }

        return new SamplerResult(
            true,
            0x01,
            [
                new SamplerTypeTag('const'),
                new SamplerParamTag('True')
            ]
        );
    }
}
