<?php
declare(strict_types=1);

namespace Jaeger\Span;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Tracer\InjectableInterface;
use Jaeger\Tracer\ResettableInterface;

class StackSpanManager implements SpanManagerInterface
{
    private $stack;

    private $context;

    public function __construct()
    {
        $this->stack = new \SplStack();
    }

    /**
     * @return self
     */
    public function reset(): ResettableInterface
    {
        $this->stack = new \SplStack();

        return $this;
    }

    /**
     * @param SpanContext $context
     *
     * @return self
     */
    public function assign(SpanContext $context): InjectableInterface
    {
        $this->context = $context;

        return $this->reset();
    }

    /**
     * @param SpanContext $context
     *
     * @return self
     */
    public function remove(SpanContext $context): InjectableInterface
    {
        while ($this->stack->valid()) {
            if (spl_object_hash($this->stack->top()) !== spl_object_hash($context)) {
                $this->stack->pop();
                continue;
            }
            break;
        }

        return $this;
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
        return ($span = $this->getSpan()) ? $span->getContext() : $this->context;
    }
}
