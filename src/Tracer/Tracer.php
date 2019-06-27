<?php
declare(strict_types=1);

namespace Jaeger\Tracer;

use Jaeger\Client\ClientInterface;
use Jaeger\Span\Context\ContextAwareInterface;
use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\Factory\SpanFactoryInterface;
use Jaeger\Span\SpanInterface;
use Jaeger\Span\SpanManager;

class Tracer implements
    TracerInterface,
    ContextAwareInterface,
    InjectableInterface,
    FlushableInterface,
    ResettableInterface,
    DebuggableInterface
{
    private $manager;

    private $debugId = '';

    private $factory;

    private $client;

    public function __construct(SpanManager $manager, SpanFactoryInterface $factory, ClientInterface $client)
    {
        $this->manager = $manager;
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
        $this->manager->assign($context);

        return $this;
    }

    public function reset(): ResettableInterface
    {
        $this->manager->reset();

        return $this;
    }

    public function remove(SpanContext $context): InjectableInterface
    {
        $this->manager->remove($context);

        return $this;
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    public function debug(string $operationName, array $tags = []): SpanInterface
    {
        $span = $this->factory->parent($this, $operationName, str_shuffle('01234567890abcdef'), $tags);
        $this->manager->push($span);

        return $span;
    }

    public function start(string $operationName, array $tags = [], SpanContext $userContext = null): SpanInterface
    {
        if (null === ($context = $userContext ?: $this->manager->getContext())) {
            $span = $this->factory->parent($this, $operationName, $this->debugId, $tags);
        } else {
            $span = $this->factory->child($this, $operationName, $context, $tags);
        }
        $this->manager->push($span);

        return $span;
    }

    public function getContext(SpanContext $userContext = null): ?SpanContext
    {
        return $this->manager->getContext();
    }

    public function finish(SpanInterface $span, int $duration = 0): void
    {
        if (-1 !== $duration) {
            $span->finish($duration);

            return;
        }
        $this->manager->pop();
        if (false === $span->isSampled()) {
            return;
        }
        $this->client->add($span);
    }
}
