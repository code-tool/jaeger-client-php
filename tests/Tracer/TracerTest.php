<?php

declare(strict_types=1);

namespace Jaeger\Tests\Tracer;

use Jaeger\Sampler\ConstSampler;
use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\Factory\SpanFactory;
use Jaeger\Span\StackSpanManager;
use Jaeger\Tag\ComponentTag;
use Jaeger\Tests\Fixture\RecordingClient;
use Jaeger\Tests\Fixture\SequenceIdGenerator;
use Jaeger\Thrift\Tag;
use Jaeger\Tracer\Tracer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Tracer::class)]
final class TracerTest extends TestCase
{
    private RecordingClient $client;

    private StackSpanManager $manager;

    protected function setUp(): void
    {
        $this->client = new RecordingClient();
        $this->manager = new StackSpanManager();
    }

    public function testShouldStartARootSpanWhenNothingIsActive(): void
    {
        $tracer = $this->makeTracer();

        $span = $tracer->start('an-operation');

        self::assertSame(0, $span->parentSpanId, 'no active context means no parent');
        self::assertSame($span, $this->manager->getSpan());
        $tracer->finish($span);
    }

    public function testShouldNestASecondSpanUnderTheFirst(): void
    {
        $tracer = $this->makeTracer();
        $parent = $tracer->start('parent');

        $child = $tracer->start('child');

        self::assertSame((int) $parent->spanId, (int) $child->parentSpanId);
        self::assertSame((int) $parent->traceIdLow, (int) $child->traceIdLow);
        $tracer->finish($child);
        $tracer->finish($parent);
    }

    public function testShouldUseAnExplicitContextAsTheParent(): void
    {
        $tracer = $this->makeTracer();
        $context = new SpanContext(7, 8, 9, 10, 1);

        $span = $tracer->start('an-operation', [], $context);

        self::assertSame(7, $span->traceIdHigh);
        self::assertSame(8, $span->traceIdLow);
        self::assertSame(9, $span->parentSpanId);
        $tracer->finish($span);
    }

    public function testShouldPassTagsThroughToTheSpan(): void
    {
        $tracer = $this->makeTracer();

        $span = $tracer->start('an-operation', [new ComponentTag('db')]);

        /** @var array<array-key, Tag> $tags */
        $tags = $span->tags;
        $keys = array_map(static fn(Tag $tag): string => (string) $tag->key, $tags);
        self::assertContains('component', $keys);
        $tracer->finish($span);
    }

    public function testShouldHandASampledSpanToTheClientOnFinish(): void
    {
        $tracer = $this->makeTracer(sampled: true);
        $span = $tracer->start('an-operation');

        $tracer->finish($span);

        self::assertSame([$span], $this->client->getSpans());
    }

    public function testShouldNotReportAnUnsampledSpan(): void
    {
        $tracer = $this->makeTracer(sampled: false);
        $span = $tracer->start('an-operation');

        $tracer->finish($span);

        self::assertSame([], $this->client->getSpans());
    }

    public function testShouldPopTheSpanFromTheManagerOnFinish(): void
    {
        $tracer = $this->makeTracer();
        $span = $tracer->start('an-operation');

        $tracer->finish($span);

        self::assertNull($this->manager->getSpan());
    }

    public function testShouldDelegateFlushToTheClient(): void
    {
        $tracer = $this->makeTracer();

        self::assertSame($tracer, $tracer->flush());

        self::assertSame(1, $this->client->flushCount());
    }

    public function testShouldExposeItsClient(): void
    {
        $tracer = $this->makeTracer();

        self::assertSame($this->client, $tracer->getClient());
    }

    public function testShouldForceSamplingWhileDebugIsEnabled(): void
    {
        $tracer = $this->makeTracer(sampled: false);

        self::assertSame($tracer, $tracer->enable('debug-id-42'));
        $span = $tracer->start('an-operation');

        self::assertSame(0x03, $span->flags);
        $tracer->finish($span);
    }

    public function testShouldStopForcingSamplingOnceDebugIsDisabled(): void
    {
        $tracer = $this->makeTracer(sampled: false);
        $tracer->enable('debug-id-42');

        self::assertSame($tracer, $tracer->disable());
        $span = $tracer->start('an-operation');

        self::assertSame(0, $span->flags);
        $tracer->finish($span);
    }

    public function testShouldStartADebugSpanWithARandomDebugId(): void
    {
        $tracer = $this->makeTracer(sampled: false);

        $span = $tracer->debug('an-operation');

        self::assertSame(0x03, $span->flags);
        self::assertSame($span, $this->manager->getSpan());
        $tracer->finish($span);
    }

    public function testShouldExposeTheActiveContext(): void
    {
        $tracer = $this->makeTracer();
        $span = $tracer->start('an-operation');

        self::assertSame($span->getContext(), $tracer->getContext());
        $tracer->finish($span);
    }

    public function testShouldAdoptAnInjectedContext(): void
    {
        $tracer = $this->makeTracer();
        $context = new SpanContext(7, 8, 9, 10, 1);

        self::assertSame($tracer, $tracer->assign($context));

        self::assertSame($context, $tracer->getContext());
    }

    public function testShouldClearItsStateOnReset(): void
    {
        $tracer = $this->makeTracer();
        $tracer->assign(new SpanContext(7, 8, 9, 10, 1));
        $tracer->start('an-operation');

        self::assertSame($tracer, $tracer->reset());

        self::assertNull($tracer->getContext());
    }

    public function testShouldDelegateContextRemovalToTheManager(): void
    {
        $tracer = $this->makeTracer();
        $span = $tracer->start('an-operation');
        $context = $span->getContext();
        self::assertInstanceOf(SpanContext::class, $context);

        self::assertSame($tracer, $tracer->remove($context));

        $tracer->finish($span);
    }

    private function makeTracer(bool $sampled = true): Tracer
    {
        return new Tracer(
            $this->manager,
            new SpanFactory(new SequenceIdGenerator(range(1, 200)), new ConstSampler($sampled)),
            $this->client,
        );
    }
}
