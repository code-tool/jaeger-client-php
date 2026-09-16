<?php

declare(strict_types=1);

namespace Jaeger\Sampler;

class AdaptiveSampler implements SamplerInterface
{
    public function __construct(
        private readonly SamplerInterface $rateLimit,
        private readonly SamplerInterface $probabilistic,
    ) {}

    public function decide(int $tracerId, string $operationName, string $debugId): SamplerResult
    {
        $rateLimitResult = $this->rateLimit->decide($tracerId, $operationName, $debugId);
        if ($rateLimitResult->isSampled()) {
            return new SamplerResult(
                true,
                $rateLimitResult->getFlags(),
                array_merge([new SamplerTypeTag('adaptive'),], $rateLimitResult->getTags()),
            );
        }

        $probabilisticResult = $this->probabilistic->decide($tracerId, $operationName, $debugId);
        if ($probabilisticResult->isSampled()) {
            return new SamplerResult(
                true,
                $rateLimitResult->getFlags(),
                array_merge([new SamplerTypeTag('adaptive'),], $rateLimitResult->getTags()),
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
