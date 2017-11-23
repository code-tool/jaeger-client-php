<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Tracer;

use CodeTool\OpenTracing\Client\ClientInterface;
use CodeTool\OpenTracing\Span\Context\SpanContext;
use CodeTool\OpenTracing\Span\Factory\SpanFactoryInterface;
use CodeTool\OpenTracing\Span\SpanInterface;
use Ds\Stack;

class Tracer implements TracerInterface, ExtractorInterface, InjectorInterface, FlushableInterface
{
    private $stack;

    private $factory;

    private $client;

    public function __construct(Stack $stack, SpanFactoryInterface $factory, ClientInterface $client)
    {
        $this->stack = $stack;
        $this->factory = $factory;
        $this->client = $client;
    }

    public function flush(): FlushableInterface
    {
        $this->client->flush();
        if (0 !== $this->stack->count()) {
            throw new \RuntimeException('Corrupted stack');
        }

        return $this;
    }

    public function assign(SpanContext $context): ExtractorInterface
    {
        $this->stack->push([$context]);

        return $this;
    }

    public function getContext(): ?SpanContext
    {
        if (0 === $this->stack->count()) {
            return null;
        }

        return $this->stack->peek();
    }

    public function start(string $operationName, array $tags = []): SpanInterface
    {
        $span = $this->factory->create($operationName, $tags, $this->getContext());
        $this->stack->push($span->getContext());

        return $span;
    }

    public function finish(SpanInterface $span): TracerInterface
    {
        $this->client->add($span->finish());
        $this->stack->pop();

        return $this;
    }
}
