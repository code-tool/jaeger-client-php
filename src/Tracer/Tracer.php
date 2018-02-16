<?php
declare(strict_types=1);

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

    public function enable(string $debugId): DebuggableInterface
    {
        $this->debugId = $debugId;

        return $this;
    }

    public function disable(): DebuggableInterface
    {
        $this->debugId = '';

        return $this;
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

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    public function debug(string $operationName, array $tags = []): SpanInterface
    {
        $span = $this->factory->parent($this, $operationName, str_shuffle('01234567890abcdef'), $tags);
        $this->stack->push($span->getContext());

        return $span;
    }

    public function start(string $operationName, array $tags = [], SpanContext $userContext = null): SpanInterface
    {
        if (null === ($context = $this->getContext($userContext))) {
            $span = $this->factory->parent($this, $operationName, $this->debugId, $tags);
        } else {
            $span = $this->factory->child($this, $operationName, $context, $tags);
        }
        $this->stack->push($span->getContext());

        return $span;
    }

    public function getContext(SpanContext $userContext = null): ?SpanContext
    {
        if (null !== $userContext) {
            return $userContext;
        }

        if (0 !== $this->stack->count()) {
            return $this->stack->top();
        }

        return null;
    }

    public function finish(SpanInterface $span, int $duration = 0): TracerInterface
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
