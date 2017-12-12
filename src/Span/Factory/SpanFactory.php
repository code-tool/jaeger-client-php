<?php
declare(strict_types=1);

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

    public function create(
        string $operationName,
        array $tags = [],
        SpanContext $parentContext = null,
        array $logs = []
    ): SpanInterface {
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
