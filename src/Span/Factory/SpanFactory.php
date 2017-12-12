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
            $traceId = $spanId;
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
                (int)$traceId,
                (int)$spanId,
                (int)$parentId,
                (int)$debugId,
                (int)$flags,
                $baggage
            ),
            $operationName,
            (int)round(microtime(true) * 1000000),
            $tags,
            $logs
        );
    }
}
