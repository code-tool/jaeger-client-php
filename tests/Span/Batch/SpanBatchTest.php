<?php

declare(strict_types=1);

namespace Jaeger\Tests\Span\Batch;

use Jaeger\Process\CliProcess;
use Jaeger\Span\Batch\SpanBatch;
use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\Span;
use Jaeger\Tests\Fixture\RecordingTracer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SpanBatch::class)]
final class SpanBatchTest extends TestCase
{
    public function testShouldCarryItsProcessAndSpans(): void
    {
        $tracer = new RecordingTracer();
        $process = new CliProcess('a-service');
        $spans = [
            new Span($tracer, new SpanContext(1, 2, 3, 4), 'first', 1),
            new Span($tracer, new SpanContext(1, 2, 5, 3), 'second', 2),
        ];

        $batch = new SpanBatch($process, $spans);

        self::assertSame($process, $batch->process);
        self::assertSame($spans, $batch->spans);
        foreach ($spans as $span) {
            $span->finish();
        }
    }

    public function testShouldAcceptAnEmptySpanList(): void
    {
        $batch = new SpanBatch(new CliProcess('a-service'));

        self::assertSame([], $batch->spans);
    }
}
