<?php
declare(strict_types=1);

namespace Jaeger\Tracer;

use Jaeger\Client\ClientInterface;
use Jaeger\Span\Context\ContextAwareInterface;
use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\Factory\SpanFactoryInterface;
use Jaeger\Span\SpanInterface;

class Tracer implements TracerInterface, ContextAwareInterface, InjectableInterface, FlushableInterface
{
    private $context;

    private $stack;

    private $factory;

    private $client;

    public function __construct(\SplStack $stack, SpanFactoryInterface $factory, ClientInterface $client)
    {
        $this->stack = $stack;
        $this->factory = $factory;
        $this->client = $client;
    }

    public function flush(): FlushableInterface
    {
        if (0 !== $this->stack->count()) {
            trigger_error('You are flushing non-empty tracer stack, some span(-s) were started but not finished');
        }
        $this->client->flush();

        return $this;
    }

    public function assign(SpanContext $context): InjectableInterface
    {
        $this->context = $context;

        return $this;
    }

    public function getContext(): ?SpanContext
    {
        return $this->context;
    }

    public function debug(string $operationName, array $tags = []): SpanInterface
    {
        $span = $this->factory->parent($operationName, true, $tags);
        $this->stack->push($span->getContext());
        $this->context = $this->stack->top();

        return $span;
    }


    public function start(string $operationName, array $tags = [], SpanContext $context = null): SpanInterface
    {
        if (null === $context && null === $this->context) {
            $span = $this->factory->parent($operationName, false, $tags);
        } else {
            $span = $this->factory->child($operationName, $context ?? $this->context, $tags);
        }
        $this->stack->push($span->getContext());
        $this->context = $this->stack->top();

        return $span;
    }

    public function finish(SpanInterface $span, int $duration = 0): TracerInterface
    {
        $this->stack->pop();
        $this->context = $this->stack->count() ? $this->stack->top() : null;
        if (false === $span->isSampled()) {
            return $this;
        }
        $this->client->add($span->finish($duration));

        return $this;
    }
}
