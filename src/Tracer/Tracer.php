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
        $this->client->flush();

        return $this;
    }

    public function assign(SpanContext $context): InjectableInterface
    {
        $this->stack->push($context);

        return $this;
    }

    public function getContext(): ?SpanContext
    {
        if (0 === $this->stack->count()) {
            return null;
        }

        return $this->stack->top();
    }

    public function start(string $operationName, array $tags = [], SpanContext $context = null): SpanInterface
    {
        $span = $this->factory->create($operationName, $tags, $context ?? $this->getContext());
        $this->stack->push($span->getContext());

        return $span;
    }

    public function finish(SpanInterface $span, int $duration = 0): TracerInterface
    {
        $this->client->add($span->finish($duration));
        $this->stack->pop();

        return $this;
    }
}
