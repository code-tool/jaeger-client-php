<?php

declare(strict_types=1);

namespace Jaeger\Tests\Span\Factory;

use Jaeger\Sampler\ConstSampler;
use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\Factory\SpanFactory;
use Jaeger\Tag\ComponentTag;
use Jaeger\Tests\Fixture\RecordingTracer;
use Jaeger\Tests\Fixture\SequenceIdGenerator;
use Jaeger\Thrift\Log;
use Jaeger\Thrift\Tag;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SpanFactory::class)]
final class SpanFactoryTest extends TestCase
{
    /**
     * parent() draws three ids: the first is only fed to the sampler, then traceIdLow, then spanId.
     */
    public function testShouldBuildARootSpanWithoutAParent(): void
    {
        $factory = new SpanFactory(new SequenceIdGenerator([10, 20, 30]), new ConstSampler(true));

        $span = $factory->parent(new RecordingTracer(), 'an-operation', '');

        self::assertSame(0, $span->traceIdHigh, 'a 64-bit trace leaves the high half at zero');
        self::assertSame(20, $span->traceIdLow);
        self::assertSame(30, $span->spanId);
        self::assertSame(0, $span->parentSpanId);
        self::assertSame('an-operation', $span->operationName);
        $span->finish();
    }

    public function testShouldBuildA128BitTraceIdWhenAskedTo(): void
    {
        $factory = new SpanFactory(new SequenceIdGenerator([10, 20, 30, 40]), new ConstSampler(true), true);

        $span = $factory->parent(new RecordingTracer(), 'an-operation', '');

        self::assertSame(20, $span->traceIdHigh);
        self::assertSame(30, $span->traceIdLow);
        self::assertSame(40, $span->spanId);
        $span->finish();
    }

    public function testShouldStampTheSamplerFlagsOntoARootSpan(): void
    {
        $factory = new SpanFactory(new SequenceIdGenerator([1, 2, 3]), new ConstSampler(true));

        $span = $factory->parent(new RecordingTracer(), 'an-operation', '');

        self::assertSame(0x01, $span->flags);
        self::assertTrue($span->isSampled());
        $span->finish();
    }

    public function testShouldLeaveAnUnsampledRootSpanUnflagged(): void
    {
        $factory = new SpanFactory(new SequenceIdGenerator([1, 2, 3]), new ConstSampler(false));

        $span = $factory->parent(new RecordingTracer(), 'an-operation', '');

        self::assertSame(0, $span->flags);
        self::assertFalse($span->isSampled());
        $span->finish();
    }

    public function testShouldMergeCallerTagsWithSamplerTags(): void
    {
        $factory = new SpanFactory(new SequenceIdGenerator([1, 2, 3]), new ConstSampler(true));
        $callerTag = new ComponentTag('db');

        $span = $factory->parent(new RecordingTracer(), 'an-operation', '', [$callerTag]);

        /** @var array<array-key, Tag> $tags */
        $tags = $span->tags;
        $keys = array_map(static fn(Tag $tag): string => (string) $tag->key, $tags);
        self::assertContains('component', $keys);
        self::assertContains('sampler.type', $keys);
        $span->finish();
    }

    public function testShouldHonourADebugIdOnARootSpan(): void
    {
        $factory = new SpanFactory(new SequenceIdGenerator([1, 2, 3]), new ConstSampler(false));

        $span = $factory->parent(new RecordingTracer(), 'an-operation', 'debug-id-42');

        self::assertSame(0x03, $span->flags, 'a debug request is force-sampled');
        $span->finish();
    }

    public function testShouldInheritTheTraceFromTheParentContext(): void
    {
        $factory = new SpanFactory(new SequenceIdGenerator([99]), new ConstSampler(true));
        $parent = new SpanContext(1, 2, 3, 4, 1, ['key' => 'value']);

        $span = $factory->child(new RecordingTracer(), 'a-child', $parent);

        self::assertSame(1, $span->traceIdHigh);
        self::assertSame(2, $span->traceIdLow);
        self::assertSame(99, $span->spanId, 'a child gets a fresh span id');
        self::assertSame(3, $span->parentSpanId, "the parent's span id becomes the parent link");
        self::assertSame(1, $span->flags);
        $span->finish();
    }

    public function testShouldCarryParentBaggageIntoTheChild(): void
    {
        $factory = new SpanFactory(new SequenceIdGenerator([99]), new ConstSampler(true));
        $parent = new SpanContext(1, 2, 3, 4, 1, ['key' => 'value']);

        $span = $factory->child(new RecordingTracer(), 'a-child', $parent);

        self::assertSame('value', $span->getItem('key'));
        $span->finish();
    }

    public function testShouldNotConsultTheSamplerForAChildSpan(): void
    {
        $generator = new SequenceIdGenerator([99]);
        $factory = new SpanFactory($generator, new ConstSampler(false));

        $span = $factory->child(new RecordingTracer(), 'a-child', new SpanContext(1, 2, 3, 4, 1));

        self::assertSame(1, $generator->callCount(), 'only the span id is generated');
        self::assertSame(1, $span->flags, 'the child inherits the parent sampling decision');
        $span->finish();
    }

    public function testShouldKeepCallerTagsAndLogsOnAChildSpan(): void
    {
        $factory = new SpanFactory(new SequenceIdGenerator([99]), new ConstSampler(true));
        $tag = new ComponentTag('db');
        $log = new Log(['timestamp' => 1, 'fields' => []]);

        $span = $factory->child(new RecordingTracer(), 'a-child', new SpanContext(1, 2, 3, 4), [$tag], [$log]);

        self::assertSame([$tag], $span->tags, 'no sampler tags are merged into a child');
        self::assertSame([$log], $span->logs);
        $span->finish();
    }

    public function testShouldStampAStartTimeInMicroseconds(): void
    {
        $factory = new SpanFactory(new SequenceIdGenerator([1, 2, 3]), new ConstSampler(true));
        $before = (int) (microtime(true) * 1000000.0);

        $span = $factory->parent(new RecordingTracer(), 'an-operation', '');

        $after = (int) (microtime(true) * 1000000.0);
        self::assertGreaterThanOrEqual($before, $span->startTime);
        self::assertLessThanOrEqual($after, $span->startTime);
        $span->finish();
    }
}
