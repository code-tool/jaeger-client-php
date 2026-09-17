<?php

declare(strict_types=1);

namespace Jaeger\Tests\Client;

use Jaeger\Client\ThriftClient;
use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\Span;
use Jaeger\Span\SpanInterface;
use Jaeger\Tests\Fixture\RecordingAgent;
use Jaeger\Tests\Fixture\RecordingTracer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ThriftClient::class)]
final class ThriftClientTest extends TestCase
{
    private RecordingTracer $tracer;

    protected function setUp(): void
    {
        $this->tracer = new RecordingTracer();
    }

    public function testShouldStartWithNoSpans(): void
    {
        self::assertSame([], new ThriftClient('a-service', new RecordingAgent())->getSpans());
    }

    public function testShouldCollectSpansWithoutEmittingThem(): void
    {
        $agent = new RecordingAgent();
        $client = new ThriftClient('a-service', $agent);
        $span = $this->makeSpan();

        self::assertSame($client, $client->add($span));

        self::assertSame([$span], $client->getSpans());
        self::assertSame([], $agent->batches(), 'nothing leaves the client until flush()');
        $span->finish();
    }

    public function testShouldEmitCollectedSpansOnFlush(): void
    {
        $agent = new RecordingAgent();
        $client = new ThriftClient('a-service', $agent);
        $spans = [$this->makeSpan(), $this->makeSpan()];
        foreach ($spans as $span) {
            $client->add($span);
        }

        self::assertSame($client, $client->flush());

        self::assertCount(1, $agent->batches());
        self::assertSame($spans, $agent->batches()[0]->spans ?? []);
        foreach ($spans as $span) {
            $span->finish();
        }
    }

    public function testShouldNameTheProcessAfterTheService(): void
    {
        $agent = new RecordingAgent();
        $client = new ThriftClient('a-service', $agent);
        $span = $this->makeSpan();
        $client->add($span);

        $client->flush();

        self::assertSame('a-service', $agent->batches()[0]->process?->serviceName);
        $span->finish();
    }

    public function testShouldForgetItsSpansAfterFlushing(): void
    {
        $agent = new RecordingAgent();
        $client = new ThriftClient('a-service', $agent);
        $span = $this->makeSpan();
        $client->add($span);

        $client->flush();
        $client->flush();

        self::assertSame([], $client->getSpans());
        self::assertCount(1, $agent->batches(), 'a second flush has nothing left to emit');
        $span->finish();
    }

    public function testShouldEmitNothingWhenThereAreNoSpans(): void
    {
        $agent = new RecordingAgent();

        new ThriftClient('a-service', $agent)->flush();

        self::assertSame([], $agent->batches());
    }

    #[DataProvider('chunkingCases')]
    public function testShouldSplitSpansIntoBatchesOfTheConfiguredSize(
        int $spanCount,
        int $batchSize,
        int $expectedBatches,
    ): void {
        $agent = new RecordingAgent();
        $client = new ThriftClient('a-service', $agent, $batchSize);
        $spans = [];
        for ($i = 0; $i < $spanCount; $i++) {
            $span = $this->makeSpan();
            $spans[] = $span;
            $client->add($span);
        }

        $client->flush();

        self::assertCount($expectedBatches, $agent->batches());
        $emitted = 0;
        foreach ($agent->batches() as $batch) {
            $emitted += \count($batch->spans ?? []);
        }

        self::assertSame($spanCount, $emitted);
        foreach ($spans as $span) {
            $span->finish();
        }
    }

    /**
     * @return iterable<string, array{int, int, int}>
     */
    public static function chunkingCases(): iterable
    {
        yield '✅ one batch, partially full' => [3, 5, 1];
        yield '✅ exactly one full batch' => [5, 5, 1];
        yield '✅ two batches' => [6, 5, 2];
        yield '✅ every span in its own batch' => [4, 1, 4];
        yield '✅ default batch size holds 32' => [32, ThriftClient::MAX_BATCH_SIZE, 1];
        yield '✅ default batch size splits at 33' => [33, ThriftClient::MAX_BATCH_SIZE, 2];
    }

    public function testShouldDefaultToABatchSizeOfThirtyTwo(): void
    {
        self::assertSame(32, ThriftClient::MAX_BATCH_SIZE);
    }

    private function makeSpan(): SpanInterface
    {
        return new Span($this->tracer, new SpanContext(1, 2, 3, 4, 1), 'an-operation', 1);
    }
}
