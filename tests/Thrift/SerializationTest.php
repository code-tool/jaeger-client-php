<?php

declare(strict_types=1);

namespace Jaeger\Tests\Thrift;

use Jaeger\Thrift\Process;
use Jaeger\Process\CliProcess;
use Jaeger\Span\Batch\SpanBatch;
use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\Span;
use Jaeger\Tag\BinaryTag;
use Jaeger\Tag\BoolTag;
use Jaeger\Tag\DoubleTag;
use Jaeger\Tag\LongTag;
use Jaeger\Tag\StringTag;
use Jaeger\Tests\Fixture\RecordingTracer;
use Jaeger\Tests\Fixture\UdpListener;
use Jaeger\Thrift\Agent\AgentClient;
use Jaeger\Thrift\Batch;
use Jaeger\Thrift\Log;
use Jaeger\Thrift\SpanRef;
use Jaeger\Thrift\SpanRefType;
use Jaeger\Thrift\TagType;
use Jaeger\Transport\TUDPTransport;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Protocol\TCompactProtocol;
use Thrift\Protocol\TProtocol;
use Thrift\Transport\TMemoryBuffer;

/**
 * Exercises the generated Thrift structs against the apache/thrift runtime.
 * These guard the wire format itself, which is what a Jaeger agent actually consumes.
 */
#[CoversNothing]
final class SerializationTest extends TestCase
{
    /**
     * @return iterable<string, array{class-string<TProtocol>}>
     */
    public static function protocolCases(): iterable
    {
        yield '✅ binary protocol' => [TBinaryProtocol::class];
        yield '✅ compact protocol' => [TCompactProtocol::class];
    }

    /**
     * @param class-string<TProtocol> $protocolClass
     */
    #[DataProvider('protocolCases')]
    public function testShouldRoundTripABatchThroughTheWire(string $protocolClass): void
    {
        $batch = $this->makeBatch();
        $buffer = new TMemoryBuffer();

        $batch->write(new $protocolClass($buffer));
        $decoded = new Batch();
        $decoded->read(new $protocolClass(new TMemoryBuffer($buffer->getBuffer())));

        self::assertSame('a-service', self::processOf($decoded)->serviceName);
        self::assertCount(1, self::spansOf($decoded));
        self::assertSame('an-operation', self::firstSpanOf($decoded)->operationName);
    }

    /**
     * @param class-string<TProtocol> $protocolClass
     */
    #[DataProvider('protocolCases')]
    public function testShouldPreserveEveryIdentifierIncludingNegativeOnes(string $protocolClass): void
    {
        $span = $this->firstSpanOf($this->roundTrip($this->makeBatch(), $protocolClass));

        self::assertSame(-1, $span->traceIdLow);
        self::assertSame(PHP_INT_MAX, $span->traceIdHigh);
        self::assertSame(3, $span->spanId);
        self::assertSame(4, $span->parentSpanId);
        self::assertSame(1, $span->flags);
    }

    /**
     * @param class-string<TProtocol> $protocolClass
     */
    #[DataProvider('protocolCases')]
    public function testShouldPreserveEveryTagType(string $protocolClass): void
    {
        $span = $this->firstSpanOf($this->roundTrip($this->makeBatch(), $protocolClass));

        $tags = [];
        foreach ($span->tags ?? [] as $tag) {
            $tags[(string) $tag->key] = [$tag->vType, $tag->vStr ?? $tag->vDouble ?? $tag->vBool ?? $tag->vLong ?? $tag->vBinary];
        }

        self::assertSame([TagType::STRING, 'a value'], $tags['t.str']);
        self::assertSame([TagType::DOUBLE, 3.5], $tags['t.dbl']);
        self::assertSame([TagType::BOOL, true], $tags['t.bool']);
        self::assertSame([TagType::LONG, PHP_INT_MAX], $tags['t.long']);
        self::assertSame([TagType::BINARY, "\x00\x01\xff\xfe"], $tags['t.bin']);
    }

    /**
     * @param class-string<TProtocol> $protocolClass
     */
    #[DataProvider('protocolCases')]
    public function testShouldPreserveLogsAndReferences(string $protocolClass): void
    {
        $span = $this->firstSpanOf($this->roundTrip($this->makeBatch(), $protocolClass));

        $logs = $span->logs ?? [];
        $references = $span->references ?? [];
        self::assertCount(1, $logs);
        self::assertSame(1_700_000_000_000_000, $logs[0]->timestamp);
        self::assertCount(1, $references);
        self::assertSame(SpanRefType::CHILD_OF, $references[0]->refType);
        self::assertSame(9, $references[0]->spanId);
    }

    /**
     * @param class-string<TProtocol> $protocolClass
     */
    #[DataProvider('protocolCases')]
    public function testShouldPreserveNonAsciiText(string $protocolClass): void
    {
        $process = $this->processOf($this->roundTrip($this->makeBatch(), $protocolClass));

        $tags = [];
        foreach ($process->tags ?? [] as $tag) {
            $tags[(string) $tag->key] = $tag->vStr;
        }

        self::assertSame('піднімай ✓', $tags['unicode']);
    }

    public function testShouldEmitARealBatchOverUdp(): void
    {
        $listener = new UdpListener();
        $transport = new TUDPTransport('127.0.0.1', $listener->port());
        $agent = new AgentClient(new TCompactProtocol($transport));

        try {
            $agent->emitBatch($this->makeBatch());
            $transport->flush();

            $datagram = $listener->receive();

            self::assertIsString($datagram);
            self::assertStringContainsString('an-operation', $datagram);
            self::assertStringContainsString('a-service', $datagram);
        } finally {
            $transport->close();
            $listener->close();
        }
    }

    public function testShouldEmitARealSpanBatchBuiltFromLibraryObjects(): void
    {
        $listener = new UdpListener();
        $transport = new TUDPTransport('127.0.0.1', $listener->port());
        $agent = new AgentClient(new TCompactProtocol($transport));
        $tracer = new RecordingTracer();
        $span = new Span($tracer, new SpanContext(1, 2, 3, 4, 1), 'library-span', 1_700_000_000_000_000);

        try {
            $agent->emitBatch(new SpanBatch(new CliProcess('a-service'), [$span]));
            $transport->flush();

            $datagram = $listener->receive();

            self::assertIsString($datagram);
            self::assertStringContainsString('library-span', $datagram);
        } finally {
            $span->finish();
            $transport->close();
            $listener->close();
        }
    }

    /**
     * @param class-string<TProtocol> $protocolClass
     */
    #[DataProvider('protocolCases')]
    public function testShouldRoundTripAnEmptyBatch(string $protocolClass): void
    {
        $decoded = $this->roundTrip(new Batch(['process' => new CliProcess('a-service'), 'spans' => []]), $protocolClass);

        self::assertSame([], $decoded->spans);
        self::assertSame('a-service', $this->processOf($decoded)->serviceName);
    }

    /**
     * Every field read below is `required` in the IDL, so a round trip must return it non-null.
     */
    private function processOf(Batch $batch): Process
    {
        $process = $batch->process;
        self::assertInstanceOf(Process::class, $process);

        return $process;
    }

    /**
     * @return array<array-key, \Jaeger\Thrift\Span>
     */
    private function spansOf(Batch $batch): array
    {
        $spans = $batch->spans;
        self::assertIsArray($spans);

        return $spans;
    }

    private function firstSpanOf(Batch $batch): \Jaeger\Thrift\Span
    {
        $spans = $this->spansOf($batch);
        self::assertArrayHasKey(0, $spans);

        return $spans[0];
    }

    /**
     * @param class-string<TProtocol> $protocolClass
     */
    private function roundTrip(Batch $batch, string $protocolClass): Batch
    {
        $buffer = new TMemoryBuffer();
        $batch->write(new $protocolClass($buffer));

        $decoded = new Batch();
        $decoded->read(new $protocolClass(new TMemoryBuffer($buffer->getBuffer())));

        return $decoded;
    }

    private function makeBatch(): Batch
    {
        $tags = [
            new StringTag('t.str', 'a value'),
            new DoubleTag('t.dbl', 3.5),
            new BoolTag('t.bool', true),
            new LongTag('t.long', PHP_INT_MAX),
            new BinaryTag('t.bin', "\x00\x01\xff\xfe"),
        ];

        $span = new \Jaeger\Thrift\Span([
            'traceIdLow' => -1,
            'traceIdHigh' => PHP_INT_MAX,
            'spanId' => 3,
            'parentSpanId' => 4,
            'operationName' => 'an-operation',
            'flags' => 1,
            'startTime' => 1_700_000_000_000_000,
            'duration' => 1234,
            'tags' => $tags,
            'logs' => [new Log(['timestamp' => 1_700_000_000_000_000, 'fields' => $tags])],
            'references' => [new SpanRef([
                'refType' => SpanRefType::CHILD_OF,
                'traceIdLow' => 7,
                'traceIdHigh' => 8,
                'spanId' => 9,
            ])],
        ]);

        return new Batch([
            'process' => new Process([
                'serviceName' => 'a-service',
                'tags' => [new StringTag('unicode', 'піднімай ✓')],
            ]),
            'spans' => [$span],
        ]);
    }
}
