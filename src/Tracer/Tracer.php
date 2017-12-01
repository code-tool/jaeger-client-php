<?php

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

    /**
     * @return FlushableInterface
     */
    public function flush()
    {
        $this->client->flush();
        if (0 !== $this->stack->count()) {
            throw new \RuntimeException('Corrupted stack');
        }

        return $this;
    }

    /**
     * @param SpanContext $context
     *
     * @return InjectableInterface
     */
    public function assign(SpanContext $context)
    {
        $this->stack->push([$context]);

        return $this;
    }

    /**
     * @return SpanContext|null
     */
    public function getContext()
    {
        if (0 === $this->stack->count()) {
            return null;
        }

        return $this->stack->top();
    }

    /**
     * @param string $operationName
     * @param array  $tags
     *
     * @return SpanInterface
     */
    public function start($operationName, array $tags = [])
    {
        $span = $this->factory->create($operationName, $tags, $this->getContext());
        $this->stack->push($span->getContext());

        return $span;
    }

    /**
     * @param SpanInterface $span
     *
     * @return TracerInterface
     */
    public function finish(SpanInterface $span)
    {
        $this->client->add($span->finish());
        $this->stack->pop();

        return $this;
    }
}
