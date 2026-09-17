<?php

declare(strict_types=1);

namespace Jaeger\Sampler;

class ConstSampler extends AbstractSampler
{
    public function __construct(private readonly bool $debugEnabled) {}

    public function doDecide(int $tracerId, string $operationName): SamplerResult
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
                ],
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
            ],
        );
    }
}
