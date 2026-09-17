<?php

declare(strict_types=1);

namespace Jaeger\Tests\Span;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\Span;
use Jaeger\Tag\ComponentTag;
use Jaeger\Tag\StringTag;
use Jaeger\Tests\Fixture\RecordingTracer;
use Jaeger\Thrift\Log;
use Jaeger\Thrift\Tag;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Span::class)]
final class SpanTest extends TestCase
{
    public function testShouldCopyIdentifiersOutOfItsContext(): void
    {
        $span = $this->makeSpan(new RecordingTracer(), new SpanContext(1, 2, 3, 4, 1));

        self::assertSame(2, $span->traceIdLow);
        self::assertSame(1, $span->traceIdHigh);
        self::assertSame(3, $span->spanId);
        self::assertSame(4, $span->parentSpanId);
        self::assertSame(1, $span->flags);
        $span->finish();
    }

    public function testShouldCarryItsOperationNameAndStartTime(): void
    {
        $span = $this->makeSpan(new RecordingTracer(), startTime: 1_700_000_000_000_000);

        self::assertSame('an-operation', $span->operationName);
        self::assertSame(1_700_000_000_000_000, $span->startTime);
        $span->finish();
    }

    public function testShouldExposeItsContext(): void
    {
        $context = new SpanContext(1, 2, 3, 4);
        $span = $this->makeSpan(new RecordingTracer(), $context);

        self::assertSame($context, $span->getContext());
        $span->finish();
    }

    public function testShouldReportSampledFromItsContextFlags(): void
    {
        $tracer = new RecordingTracer();
        $sampled = $this->makeSpan($tracer, new SpanContext(1, 2, 3, 4, 1));
        $unsampled = $this->makeSpan($tracer, new SpanContext(1, 2, 3, 4, 0));

        self::assertTrue($sampled->isSampled());
        self::assertFalse($unsampled->isSampled());
        $sampled->finish();
        $unsampled->finish();
    }

    public function testShouldOverwriteItsStartTime(): void
    {
        $span = $this->makeSpan(new RecordingTracer(), startTime: 1);

        self::assertSame($span, $span->start(999));
        self::assertSame(999, $span->startTime);
        $span->finish();
    }

    public function testShouldUseAnExplicitDurationWhenGiven(): void
    {
        $span = $this->makeSpan(new RecordingTracer());

        $span->finish(1234);

        self::assertSame(1234, $span->duration);
    }

    public function testShouldComputeADurationFromTheClockWhenNoneIsGiven(): void
    {
        $span = $this->makeSpan(new RecordingTracer(), startTime: (int) (microtime(true) * 1000000.0));

        $span->finish();

        self::assertIsInt($span->duration);
        self::assertGreaterThanOrEqual(0, $span->duration);
    }

    public function testShouldNotifyItsTracerOnFinish(): void
    {
        $tracer = new RecordingTracer();
        $span = $this->makeSpan($tracer);

        $span->finish(10);

        self::assertSame(1, $tracer->finishedCount());
        [$finishedSpan, $duration] = $tracer->finishedCalls()[0];
        self::assertSame($span, $finishedSpan);
        self::assertSame(-1, $duration);
    }

    public function testShouldAccumulateTags(): void
    {
        $span = $this->makeSpan(new RecordingTracer());
        $tag = new ComponentTag('db');

        self::assertSame($span, $span->addTag($tag));

        self::assertContains($tag, $span->tags ?? []);
        $span->finish();
    }

    public function testShouldAccumulateLogs(): void
    {
        $span = $this->makeSpan(new RecordingTracer());
        $log = new Log(['timestamp' => 1, 'fields' => []]);

        self::assertSame($span, $span->addLog($log));

        self::assertContains($log, $span->logs ?? []);
        $span->finish();
    }

    public function testShouldStartFromTheTagsAndLogsItWasGiven(): void
    {
        $tag = new StringTag('a', 'b');
        $log = new Log(['timestamp' => 1, 'fields' => []]);
        $span = new Span(new RecordingTracer(), new SpanContext(1, 2, 3, 4), 'an-operation', 1, [$tag], [$log]);

        self::assertSame([$tag], $span->tags);
        self::assertSame([$log], $span->logs);
        $span->finish();
    }

    public function testShouldSwapInANewContextWhenBaggageIsAdded(): void
    {
        $span = $this->makeSpan(new RecordingTracer());
        $before = $span->getContext();

        self::assertSame($span, $span->withItem('key', 'value'));

        self::assertNotSame($before, $span->getContext());
        self::assertSame('value', $span->getItem('key'));
        $span->finish();
    }

    public function testShouldSwapInANewContextWhenBaggageIsRemoved(): void
    {
        $span = $this->makeSpan(new RecordingTracer());
        $span->withItem('key', 'value');

        self::assertSame($span, $span->withoutItem('key'));

        self::assertNull($span->getItem('key'));
        $span->finish();
    }

    public function testShouldFallBackToTheDefaultForUnknownBaggage(): void
    {
        $span = $this->makeSpan(new RecordingTracer());

        self::assertSame('fallback', $span->getItem('missing', 'fallback'));
        $span->finish();
    }

    /**
     * A span that leaves scope without being finished reports itself as an error.
     */
    public function testShouldSelfReportWhenDestroyedUnfinished(): void
    {
        $tracer = new RecordingTracer();

        (function () use ($tracer): void {
            $this->makeSpan($tracer);
        })();

        gc_collect_cycles();

        self::assertSame(1, $tracer->finishedCount());
        /** @var array<array-key, Tag> $tags */
        $tags = $tracer->finishedCalls()[0][0]->tags;
        $keys = array_map(static fn(Tag $tag): string => (string) $tag->key, $tags);
        self::assertContains('error', $keys);
        self::assertContains('scope.missing', $keys);
    }

    public function testShouldNotSelfReportWhenAlreadyFinished(): void
    {
        $tracer = new RecordingTracer();

        (function () use ($tracer): void {
            $this->makeSpan($tracer)->finish(5);
        })();

        gc_collect_cycles();

        self::assertSame(1, $tracer->finishedCount(), 'only the explicit finish() is reported');
    }

    private function makeSpan(
        RecordingTracer $tracer,
        ?SpanContext $context = null,
        int $startTime = 1_700_000_000_000_000,
    ): Span {
        return new Span($tracer, $context ?? new SpanContext(1, 2, 3, 4), 'an-operation', $startTime);
    }
}
