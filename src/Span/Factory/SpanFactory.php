<?php
declare(strict_types=1);

namespace Jaeger\Span\Factory;

use Jaeger\Id\IdGeneratorInterface;
use Jaeger\Sampler\SamplerInterface;
use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\Span;
use Jaeger\Span\SpanInterface;
use Jaeger\Tracer\TracerInterface;

class SpanFactory implements SpanFactoryInterface
{
    private IdGeneratorInterface $idGenerator;

    private SamplerInterface $sampler;

    private bool $trace128;

    public function __construct(IdGeneratorInterface $idGenerator, SamplerInterface $sampler, bool $trace128 = false)
    {
        $this->idGenerator = $idGenerator;
        $this->sampler = $sampler;
        $this->trace128 = $trace128;
    }

    public function parent(
        TracerInterface $tracer,
        string          $operationName,
        string          $debugId,
        array           $tags = [],
        array           $logs = []
    ): SpanInterface {
        $spanId = $this->idGenerator->next();
        $traceId = $spanId;
        $samplerResult = $this->sampler->decide($traceId, $operationName, $debugId);

        return new Span(
            $tracer,
            new SpanContext(
                $this->trace128 ? $this->idGenerator->next() : 0,
                $this->idGenerator->next(),
                $this->idGenerator->next(),
                0,
                (int)$samplerResult->getFlags()
            ),
            $operationName,
            (int)(microtime(true) * 1000000),
            array_merge($tags, $samplerResult->getTags()),
            $logs
        );
    }

    public function child(
        TracerInterface $tracer,
        string          $operationName,
        SpanContext     $parentContext,
        array           $tags = [],
        array           $logs = []
    ): SpanInterface {
        return new Span(
            $tracer,
            new SpanContext(
                $parentContext->getTraceIdHigh(),
                $parentContext->getTraceIdLow(),
                $this->idGenerator->next(),
                $parentContext->getSpanId(),
                $parentContext->getFlags(),
                $parentContext->getBaggage()
            ),
            $operationName,
            (int)(microtime(true) * 1000000),
            $tags,
            $logs
        );
    }
}
