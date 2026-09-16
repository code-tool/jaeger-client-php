<?php

declare(strict_types=1);

namespace Jaeger\Tests\Span;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\Span;
use Jaeger\Span\StackSpanManager;
use Jaeger\Tests\Fixture\RecordingTracer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(StackSpanManager::class)]
final class StackSpanManagerTest extends TestCase
{
    private RecordingTracer $tracer;

    protected function setUp(): void
    {
        $this->tracer = new RecordingTracer();
    }

    public function testShouldStartEmpty(): void
    {
        $manager = new StackSpanManager();

        self::assertNull($manager->getSpan());
        self::assertNull($manager->getContext());
    }

    public function testShouldReturnTheMostRecentlyPushedSpan(): void
    {
        $manager = new StackSpanManager();
        $first = $this->makeSpan();
        $second = $this->makeSpan();

        $manager->new($first);
        $manager->new($second);

        self::assertSame($second, $manager->getSpan());
    }

    public function testShouldPopSpansInReverseOrder(): void
    {
        $manager = new StackSpanManager();
        $first = $this->makeSpan();
        $second = $this->makeSpan();
        $manager->new($first);
        $manager->new($second);

        self::assertSame($second, $manager->finish($second));
        self::assertSame($first, $manager->finish($first));
        self::assertNull($manager->getSpan());
    }

    public function testShouldReturnNullWhenFinishingAnEmptyStack(): void
    {
        self::assertNull(new StackSpanManager()->finish($this->makeSpan()));
    }

    public function testShouldTakeItsContextFromTheCurrentSpan(): void
    {
        $manager = new StackSpanManager();
        $span = $this->makeSpan(new SpanContext(9, 8, 7, 6, 1));

        $manager->new($span);

        self::assertSame($span->getContext(), $manager->getContext());
    }

    public function testShouldFallBackToAnAssignedContextWhenNoSpanIsActive(): void
    {
        $manager = new StackSpanManager();
        $context = new SpanContext(9, 8, 7, 6, 1);

        self::assertSame($manager, $manager->assign($context));

        self::assertSame($context, $manager->getContext());
    }

    public function testShouldPreferTheActiveSpanOverAnAssignedContext(): void
    {
        $manager = new StackSpanManager();
        $assigned = new SpanContext(9, 8, 7, 6, 1);
        $span = $this->makeSpan(new SpanContext(1, 2, 3, 4, 1));

        $manager->assign($assigned);
        $manager->new($span);

        self::assertSame($span->getContext(), $manager->getContext());
    }

    public function testShouldDropAnyActiveSpansWhenAContextIsAssigned(): void
    {
        $manager = new StackSpanManager();
        $manager->new($this->makeSpan());

        $manager->assign(new SpanContext(9, 8, 7, 6));

        self::assertNull($manager->getSpan());
    }

    public function testShouldClearEverythingOnReset(): void
    {
        $manager = new StackSpanManager();
        $manager->assign(new SpanContext(9, 8, 7, 6));
        $manager->new($this->makeSpan());

        self::assertSame($manager, $manager->reset());

        self::assertNull($manager->getSpan());
        self::assertNull($manager->getContext());
    }

    public function testShouldUnwindTheStackDownToTheMatchingContext(): void
    {
        $manager = new StackSpanManager();
        $bottom = $this->makeSpan(new SpanContext(1, 2, 10, 0, 1));
        $middle = $this->makeSpan(new SpanContext(1, 2, 20, 10, 1));
        $top = $this->makeSpan(new SpanContext(1, 2, 30, 20, 1));
        $manager->new($bottom);
        $manager->new($middle);
        $manager->new($top);

        $middleContext = $middle->getContext();
        self::assertInstanceOf(SpanContext::class, $middleContext);
        self::assertSame($manager, $manager->remove($middleContext));

        self::assertSame($middle, $manager->getSpan(), 'spans above the match are discarded');
    }

    public function testShouldEmptyTheStackWhenNoSpanMatches(): void
    {
        $manager = new StackSpanManager();
        $manager->new($this->makeSpan());
        $manager->new($this->makeSpan());

        $manager->remove(new SpanContext(99, 99, 99, 99));

        self::assertNull($manager->getSpan());
    }

    public function testShouldLeaveTheStackAloneWhenTheMatchIsAlreadyOnTop(): void
    {
        $manager = new StackSpanManager();
        $manager->new($this->makeSpan());

        $top = $this->makeSpan();
        $manager->new($top);

        $topContext = $top->getContext();
        self::assertInstanceOf(SpanContext::class, $topContext);
        $manager->remove($topContext);

        self::assertSame($top, $manager->getSpan());
    }

    /**
     * Baggage changes swap a span's context for a copy, so matching must compare the trace and
     * span identifiers rather than object identity.
     */
    public function testShouldMatchAnEquivalentContextRatherThanTheSameInstance(): void
    {
        $manager = new StackSpanManager();
        $target = $this->makeSpan(new SpanContext(1, 2, 3, 4, 1));
        $manager->new($target);
        $manager->new($this->makeSpan(new SpanContext(1, 2, 5, 3, 1)));

        $manager->remove(new SpanContext(1, 2, 3, 4, 1));

        self::assertSame($target, $manager->getSpan());
    }

    public function testShouldTolerateRemovingFromAnEmptyStack(): void
    {
        $manager = new StackSpanManager();

        self::assertSame($manager, $manager->remove(new SpanContext(1, 2, 3, 4)));

        self::assertNull($manager->getSpan());
    }

    private function makeSpan(?SpanContext $context = null): Span
    {
        return new Span($this->tracer, $context ?? new SpanContext(1, 2, 3, 4), 'an-operation', 1);
    }
}
