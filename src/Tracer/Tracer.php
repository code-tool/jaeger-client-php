<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Tracer;

use CodeTool\OpenTracing\Client\ClientInterface;
use CodeTool\OpenTracing\Id\IdGeneratorInterface;
use CodeTool\OpenTracing\Span\Context\SpanContext;
use CodeTool\OpenTracing\Span\Factory\SpanFactoryInterface;
use CodeTool\OpenTracing\Span\SpanInterface;
use Ds\Stack;

class Tracer implements TracerInterface
{
    private $contextStack;

    private $spanFactory;

    private $client;

    private $idGenerator;

    public function __construct(
        Stack $stack,
        SpanFactoryInterface $factory,
        ClientInterface $client,
        IdGeneratorInterface $idGenerator
    ) {
        $this->contextStack = $stack;
        $this->spanFactory = $factory;
        $this->client = $client;
        $this->idGenerator = $idGenerator;
    }

    public function onKernelRequest(): Tracer
    {
        $this->contextStack->push(
            new SpanContext(
                $this->idGenerator->next(),
                $this->idGenerator->next(),
                0,
                $this->idGenerator->next(),
                0,
                []
            )
        );

        return $this;
    }

    public function onFinishRequest(): Tracer
    {
        $this->client->flush();

        return $this;
    }

    public function start(string $operationName, array $tags = []): SpanInterface
    {
        $span = $this->spanFactory->create($this->contextStack->peek(), $operationName, $tags);
        $this->contextStack->push($span->getContext());

        return $span;
    }

    public function finish(SpanInterface $span): TracerInterface
    {
        $this->client->add($span->finish());
        $this->contextStack->pop();

        return $this;
    }
}
