<?php

namespace Jaeger\Tracer;

use Jaeger\Client\ClientInterface;
use Jaeger\Span\Context\ContextAwareInterface;
use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\Factory\SpanFactoryInterface;
use Jaeger\Span\SpanInterface;

class Tracer implements TracerInterface,
                        ContextAwareInterface,
                        InjectableInterface,
                        FlushableInterface,
                        DebuggableInterface
{
    private $stack;

    private $debugId = '';

    private $factory;

    private $client;

    public function __construct(\SplStack $stack, SpanFactoryInterface $factory, ClientInterface $client)
    {
        $this->stack = $stack;
        $this->factory = $factory;
        $this->client = $client;
    }


    /**
     * @param string $debugId
     *
     * @return DebuggableInterface
     */
    public function enable($debugId)
    {
        $this->debugId = $debugId;

        return $this;
    }

    /**
     * @return DebuggableInterface
     */
    public function disable()
    {
        $this->debugId = '';

        return $this;
    }

    /**
     * @return FlushableInterface
     */
    public function flush()
    {
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
        $this->stack->push($context);

        return $this;
    }

    /**
     * @return SpanContext|null
     */
    public function getContext(SpanContext $userContext = null)
    {
        if (null !== $userContext) {
            return $userContext;
        }

        if (0 !== $this->stack->count()) {
            return $this->stack->top();
        }

        return null;
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * @param string $operationName
     * @param array  $tags
     *
     * @return SpanInterface
     */
    public function debug($operationName, array $tags = [])
    {
        $span = $this->factory->parent($this, $operationName, str_shuffle('01234567890abcdef'), $tags);
        $this->stack->push($span->getContext());

        return $span;
    }

    /**
     * @param string           $operationName
     * @param array            $tags
     * @param SpanContext|null $context
     *
     * @return SpanInterface
     */
    public function start($operationName, array $tags = [], SpanContext $userContext = null)
    {
        if (null === ($context = $this->getContext($userContext))) {
            $span = $this->factory->parent($this, $operationName, $this->debugId, $tags);
        } else {
            $span = $this->factory->child($this, $operationName, $context, $tags);
        }
        $this->stack->push($span->getContext());

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
        if (0 !== $this->stack->count()) {
            $this->stack->pop();
        }
        if (false === $span->finish($duration)->isSampled()) {
            return $this;
        }
        $this->client->add($span);

        return $this;
    }
}
