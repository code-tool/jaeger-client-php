<?php

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

    /**
     * @param string           $operationName
     * @param array            $tags
     * @param SpanContext|null $parentContext
     * @param array            $logs
     *
     * @return SpanInterface
     */
    public function create(
        $operationName,
        array $tags = [],
        SpanContext $parentContext = null,
        array $logs = []
    ) {
        $spanId = $this->idGenerator->next();
        if (null === $parentContext) {
            $traceId = $this->idGenerator->next();
            $debugId = 0;
            $parentId = 0;
            $flags = 0;
            $baggage = [];
            $samplerResult = $this->sampler->decide($traceId, $operationName);
            if ($samplerResult->isSampled()) {
                $flags = 0x01;
                $tags = array_merge($tags, $samplerResult->getTags());
            }
        } else {
            $traceId = $parentContext->getTraceId();
            $parentId = $parentContext->getSpanId();
            $debugId = $parentContext->getDebugId();
            $flags = $parentContext->getFlags();
            $baggage = [];
        }

        return new Span(
            new SpanContext(
                $traceId,
                $spanId,
                $parentId,
                $debugId,
                $flags,
                $baggage
            ),
            $operationName,
            (int)round(microtime(true) * 1000000),
            $tags,
            $logs
        );
    }
}
