<?php

declare(strict_types=1);

namespace Jaeger\Sampler;

class SamplerResult
{
    public function __construct(
        private readonly bool $sampled,
        private readonly int $flags,
        private readonly array $tags = [],
    ) {}

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function isSampled(): bool
    {
        return $this->sampled;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}
