<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Tracer;

use CodeTool\OpenTracing\Client\ClientInterface;
use CodeTool\OpenTracing\Id\IdGeneratorInterface;
use CodeTool\OpenTracing\Span\Context\SpanContext;
use CodeTool\OpenTracing\Span\Factory\SpanFactoryInterface;
use CodeTool\OpenTracing\Span\SpanInterface;
use CodeTool\OpenTracing\Tag\BoolTag;
use CodeTool\OpenTracing\Tag\StringTag;
use Ds\Stack;

class Tracer implements TracerInterface
{
    private $stack;

    private $factory;

    private $client;

    public function __construct(
        Stack $stack,
        SpanFactoryInterface $factory,
        ClientInterface $client,
        IdGeneratorInterface $idGenerator
    ) {
        $this->stack = $stack;
        $this->factory = $factory;
        $this->client = $client;
    }

    public function onStart(): TracerInterface
    {
        return $this;
    }

    public function onFinish(): TracerInterface
    {
        $this->client->flush();

        return $this;
    }

    public function getLocalTags()
    {
        return [
            new StringTag('jaeger.version', 'PHP'),
            new StringTag('jaeger.hostname', gethostname()),
            new StringTag('sample.type', 'const'),
            new BoolTag('sample.param', true),
        ];
    }

    public function getCurrentContext(): ?SpanContext
    {
        if (0 === $this->stack->count()) {
            return null;
        }

        return $this->stack->peek();
    }

    public function start(string $operationName, array $tags = []): SpanInterface
    {
        $span = $this->factory->create(
            $operationName,
            array_merge($this->getLocalTags(), $tags),
            $this->getCurrentContext()
        );
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
