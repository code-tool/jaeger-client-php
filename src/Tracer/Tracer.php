<?php

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

    /**
     * @return FlushableInterface
     */
    public function flush()
    {
        if (0 !== $this->stack->count()) {
            trigger_error(
                'You are flushing non-empty tracer stack, some span(-s) were started but not finished',
                E_WARNING
            );
        }
        $this->client->flush();

        return $this;
    }

    /**
     * @param SpanContext $context
     *
     * @return InjectableInterface
     */
    public function assign(SpanContext $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return SpanContext|null
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param string      $operationName
     * @param array       $tags
     * @param SpanContext $context
     *
     * @return SpanInterface
     */
    public function start($operationName, array $tags = [], SpanContext $context = null)
    {
        $context = $context ? $context : $this->context;
        $span = $this->factory->create($operationName, $tags, $context);
        $this->stack->push($span->getContext());
        $this->context = $this->stack->top();

        return $span;
    }

    /**
     * @param SpanInterface $span
     * @param int           $duration
     *
     * @return TracerInterface
     */
    public function finish(SpanInterface $span, $duration = 0)
    {
        $this->client->add($span->finish($duration));
        $this->stack->pop();
        $this->context = $this->stack->top();

        return $this;
    }
}
