<?php
declare(strict_types=1);

namespace Jaeger\Span\Factory;

use Jaeger\Id\IdGeneratorInterface;
use Jaeger\Sampler\SamplerInterface;
use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\Span;
use Jaeger\Span\SpanInterface;
use Jaeger\Tag\StringTag;

class SpanFactory implements SpanFactoryInterface
{
    private $idGenerator;

    private $sampler;

    public function __construct(IdGeneratorInterface $idGenerator, SamplerInterface $sampler)
    {
        $this->idGenerator = $idGenerator;
        $this->sampler = $sampler;
    }

    public function parent(
        string $operationName,
        bool $isDebug = false,
        array $tags = [],
        array $logs = []
    ): SpanInterface {
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
            (int)(microtime(true) * 1000000),
            array_merge($tags, $samplerResult->getTags()),
            $logs
        );
    }

    public function child(
        string $operationName,
        SpanContext $parentContext,
        array $tags = [],
        array $logs = []
    ): SpanInterface {
        $baggateTags = [];
        foreach ($parentContext->getBaggage() as $name => $value) {
            $baggateTags[] = new StringTag($name, $value);
        }

        return new Span(
            new SpanContext(
                $parentContext->getTraceId(),
                $this->idGenerator->next(),
                $parentContext->getSpanId(),
                $parentContext->getFlags(),
                $parentContext->getBaggage()
            ),
            $operationName,
            (int)(microtime(true) * 1000000),
            array_merge($tags, $baggateTags),
            $logs
        );
    }
}
