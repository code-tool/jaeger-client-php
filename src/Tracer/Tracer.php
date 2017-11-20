<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Tracer;

use CodeTool\OpenTracing\Client\ClientInterface;
use CodeTool\OpenTracing\Span\Context\SpanContext;
use CodeTool\OpenTracing\Span\Factory\SpanFactoryInterface;
use CodeTool\OpenTracing\Span\SpanInterface;
use Ds\Stack;

class Tracer implements TracerInterface
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

    public function onKernelRequest(): Tracer
    {
        $this->stack->push(new SpanContext(0, 0, 0, 0));

        return $this;
    }

    public function onFinishRequest(): Tracer
    {
        $this->client->flush();

        return $this;
    }

    public function start(string $operationName, array $tags = []): SpanInterface
    {
        /**
         * @var SpanContext $context
         */
        if (null === ($context = $this->stack->peek())) {
            throw new \RuntimeException();
        }

        $span = $this->factory->create($operationName, $context, $tags);
        $this->stack->push(
            new SpanContext(
                $context->getTraceId(),
                $context->getSpanId(),
                $span->getId(),
                $context->getDebugId()
            )
        );


        return $span;
    }

    public function finish(SpanInterface $span): TracerInterface
    {
        $this->client->add($span->finish());
        $this->stack->pop();

        return $this;
    }
}
