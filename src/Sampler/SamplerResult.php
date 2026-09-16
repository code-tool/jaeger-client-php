<?php

declare(strict_types=1);

namespace Jaeger\Sampler;

use Jaeger\Thrift\Tag;

class SamplerResult
{
    /**
     * @param array<array-key, Tag> $tags
     */
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

    /**
     * @return array<array-key, Tag>
     */
    public function getTags(): array
    {
        return $this->tags;
    }
}
