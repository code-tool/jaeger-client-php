<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Span\Factory;

use CodeTool\OpenTracing\Id\IdGeneratorInterface;
use CodeTool\OpenTracing\Sampler\SamplerInterface;
use CodeTool\OpenTracing\Span\Context\SpanContext;
use CodeTool\OpenTracing\Span\Span;
use CodeTool\OpenTracing\Span\SpanInterface;

class SpanFactory implements SpanFactoryInterface
{
    private $idGenerator;

    private $sampler;

    public function __construct(IdGeneratorInterface $idGenerator, SamplerInterface $sampler)
    {
        $this->idGenerator = $idGenerator;
        $this->sampler = $sampler;
    }

    public function sampleContext(string $operationName): SpanContext
    {
        $traceId = $this->idGenerator->next();
        $spanId = $this->idGenerator->next();
        $samplerResult = $this->sampler->decide($traceId, $operationName);
        if (false === $samplerResult->isSampled()) {
            return new SpanContext(
                $traceId,
                $spanId,
                0,
                0,
                0,
                []
            );
        }

        return new SpanContext(
            $traceId,
            $spanId,
            0,
            0,
            0x01,
            $samplerResult->getTags()
        );
    }

    public function createContext(string $operationName, SpanContext $parentContext = null): SpanContext
    {
        if (null === $parentContext) {
            return $this->sampleContext($operationName);
        }

        return new SpanContext(
            $parentContext->getTraceId(),
            $this->idGenerator->next(),
            $parentContext->getSpanId(),
            $parentContext->getDebugId(),
            $parentContext->getFlags(),
            iterator_to_array($parentContext->getIterator())
        );
    }

    public function create(
        string $operationName,
        array $tags = [],
        SpanContext $parentContext = null,
        array $logs = []
    ): SpanInterface {
        return new Span(
            $this->createContext($operationName, $parentContext),
            $operationName,
            (int)round(microtime(true) * 1000000),
            $tags,
            $logs
        );
    }
}
