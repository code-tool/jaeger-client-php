<?php

declare(strict_types=1);

namespace Jaeger\Tests\Fixture;

use Jaeger\Client\ClientInterface;
use Jaeger\Span\SpanInterface;

/**
 * A real ClientInterface implementation that keeps every span it is handed.
 */
final class RecordingClient implements ClientInterface
{
    /**
     * @var list<SpanInterface>
     */
    private array $spans = [];

    private int $flushCount = 0;

    public function add(SpanInterface $span): ClientInterface
    {
        $this->spans[] = $span;

        return $this;
    }

    /**
     * @return list<SpanInterface>
     */
    public function getSpans(): array
    {
        return $this->spans;
    }

    public function flush(): ClientInterface
    {
        $this->flushCount++;
        $this->spans = [];

        return $this;
    }

    public function flushCount(): int
    {
        return $this->flushCount;
    }
}
