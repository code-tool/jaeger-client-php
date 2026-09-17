<?php

declare(strict_types=1);

namespace Jaeger\Tests\Fixture;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\Span;
use Jaeger\Span\SpanInterface;
use Jaeger\Thrift\Tag;
use Jaeger\Tracer\TracerInterface;

/**
 * A real tracer that starts plain spans and records everything handed back to it,
 * so tests can assert on finish() without reaching for a test double.
 */
final class RecordingTracer implements TracerInterface
{
    /**
     * @var list<array{SpanInterface, int}>
     */
    private array $finished = [];

    private int $nextSpanId = 1;

    /**
     * @param array<array-key, Tag> $tags
     */
    public function start(string $operationName, array $tags = [], ?SpanContext $context = null): SpanInterface
    {
        return new Span(
            $this,
            $context ?? new SpanContext(0, 1, $this->nextSpanId++, 0, 1),
            $operationName,
            (int) (microtime(true) * 1000000.0),
            $tags,
        );
    }

    public function finish(SpanInterface $span, int $duration = 0): void
    {
        $this->finished[] = [$span, $duration];
    }

    /**
     * @return list<array{SpanInterface, int}>
     */
    public function finishedCalls(): array
    {
        return $this->finished;
    }

    public function finishedCount(): int
    {
        return \count($this->finished);
    }
}
