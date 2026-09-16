<?php

declare(strict_types=1);

namespace Jaeger\Sampler;

class AdaptiveSampler implements SamplerInterface
{
    public function __construct(
        private readonly SamplerInterface $rateLimit,
        private readonly SamplerInterface $probabilistic,
    ) {}

    public function decide(int $traceId, string $operationName, string $debugId): SamplerResult
    {
        $rateLimitResult = $this->rateLimit->decide($traceId, $operationName, $debugId);
        if ($rateLimitResult->isSampled()) {
            return new SamplerResult(
                true,
                $rateLimitResult->getFlags(),
                array_merge([new SamplerTypeTag('adaptive'),], $rateLimitResult->getTags()),
            );
        }

        $probabilisticResult = $this->probabilistic->decide($traceId, $operationName, $debugId);
        if ($probabilisticResult->isSampled()) {
            return new SamplerResult(
                true,
                $probabilisticResult->getFlags(),
                array_merge([new SamplerTypeTag('adaptive'),], $probabilisticResult->getTags()),
            );
        }

        return new SamplerResult(
            false,
            0,
            [
                new SamplerTypeTag('adaptive'),
                new SamplerDecisionTag(false),
                new SamplerFlagsTag(0x00),
            ],
        );
    }
}
