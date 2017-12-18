<?php

namespace Jaeger\Span\Factory;

use Jaeger\Id\IdGeneratorInterface;
use Jaeger\Sampler\SamplerInterface;
use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\Span;
use Jaeger\Span\SpanInterface;

class SpanFactory implements SpanFactoryInterface
{
    private $idGenerator;

    private $sampler;

    public function __construct(IdGeneratorInterface $idGenerator, SamplerInterface $sampler)
    {
        $this->idGenerator = $idGenerator;
        $this->sampler = $sampler;
    }

    /**
     * @param string $operationName
     * @param bool   $isDebug
     * @param array  $tags
     * @param array  $logs
     *
     * @return SpanInterface
     */
    public function parent(
        $operationName,
        $isDebug = false,
        array $tags = [],
        array $logs = []
    ) {
        $spanId = $this->idGenerator->next();
        $traceId = $spanId;
        $samplerResult = $this->sampler->decide($traceId, $operationName, $isDebug);

        return new Span(
            new SpanContext(
                (int)$traceId,
                (int)$spanId,
                0,
                (int)$samplerResult->getFlags()
            ),
            $operationName,
            (int)round(microtime(true) * 1000000),
            array_merge($tags, $samplerResult->getTags()),
            $logs
        );
    }

    /**
     * @param string      $operationName
     * @param SpanContext $parentContext
     * @param array       $tags
     * @param array       $logs
     *
     * @return SpanInterface
     */
    public function child(
        $operationName,
        SpanContext $parentContext,
        array $tags = [],
        array $logs = []
    ) {
        return new Span(
            new SpanContext(
                (int)$parentContext->getTraceId(),
                (int)$this->idGenerator->next(),
                (int)$parentContext->getSpanId(),
                (int)$parentContext->getFlags()
            ),
            $operationName,
            (int)round(microtime(true) * 1000000),
            $tags,
            $logs
        );
    }
}
