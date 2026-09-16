<?php

declare(strict_types=1);

namespace Jaeger\Span;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Tracer\InjectableInterface;
use Jaeger\Tracer\ResettableInterface;
use SplStack;

class StackSpanManager implements SpanManagerInterface
{
    /**
     * @var SplStack<SpanInterface>
     */
    private SplStack $stack;

    private ?SpanContext $context = null;

    public function __construct()
    {
        $this->stack = $this->createStack();
    }

    /**
     * @return self
     */
    public function reset(): ResettableInterface
    {
        $this->stack = $this->createStack();
        $this->context = null;

        return $this;
    }

    /**
     *
     * @return self
     */
    public function assign(SpanContext $context): InjectableInterface
    {
        $this->context = $context;
        $this->stack = $this->createStack();

        return $this;
    }

    /**
     *
     * @return self
     */
    public function remove(SpanContext $context): InjectableInterface
    {
        while (!$this->stack->isEmpty()) {
            $spanContext = $this->stack->top()->getContext();
            if (null !== $spanContext && $this->isSameContext($spanContext, $context)) {
                break;
            }

            $this->stack->pop();
        }

        return $this;
    }

    /**
     * A span's context is replaced by a copy whenever baggage changes, so identity is compared
     * through the trace and span identifiers rather than through the object itself.
     */
    private function isSameContext(SpanContext $left, SpanContext $right): bool
    {
        return $left->getTraceIdHigh() === $right->getTraceIdHigh()
            && $left->getTraceIdLow() === $right->getTraceIdLow()
            && $left->getSpanId() === $right->getSpanId();
    }

    public function getSpan(): ?SpanInterface
    {
        return $this->stack->count() ? $this->stack->top() : null;
    }

    public function new(SpanInterface $span): void
    {
        $this->stack->push($span);
    }

    public function finish(SpanInterface $span): ?SpanInterface
    {
        return $this->stack->count() ? $this->stack->pop() : null;
    }

    public function getContext(): ?SpanContext
    {
        return (($span = $this->getSpan()) instanceof SpanInterface) ? $span->getContext() : $this->context;
    }

    /**
     * @return SplStack<SpanInterface>
     */
    private function createStack(): SplStack
    {
        /** @var SplStack<SpanInterface> $stack */
        $stack = new SplStack();

        return $stack;
    }
}
