<?php
declare(strict_types=1);

namespace Jaeger\Sampler;

class SamplerResult
{
    private $sampled;

    private $flags;

    private $tags;

    public function __construct(bool $sampled, int $flags, array $tags = [])
    {
        $this->sampled = $sampled;
        $this->flags = $flags;
        $this->tags = $tags;
    }

    public function getFlags() : int
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
