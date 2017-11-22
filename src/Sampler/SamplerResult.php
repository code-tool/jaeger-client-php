<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Sampler;

class SamplerResult
{
    private $sampled;

    private $tags;

    public function __construct(bool $sampled, array $tags = [])
    {
        $this->sampled = $sampled;
        $this->tags = $tags;
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
