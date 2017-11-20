<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Span\Factory;

use CodeTool\OpenTracing\Id\IdGeneratorInterface;
use CodeTool\OpenTracing\Span\Context\SpanContext;
use CodeTool\OpenTracing\Span\Span;
use CodeTool\OpenTracing\Span\SpanInterface;

class SpanFactory implements SpanFactoryInterface
{
    private $idGenerator;

    public function __construct(IdGeneratorInterface $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    public function create(
        string $operationName,
        SpanContext $spanContext,
        array $tags = [],
        array $logs = []
    ): SpanInterface {

        if (0 === $spanContext->getParentId()) {
            $traceId = $this->idGenerator->next();

            return new Span(
                $traceId,
                $traceId,
                $traceId,
                0,
                $operationName,
                microtime(true) * 1000000,
                0x01,
                $tags,
                $logs
            );
        }

        if ($spanContext->isDebugStarting()) {
            $traceId = $this->idGenerator->next();
            $tags[''] = $spanContext->getDebugId();

            return new Span(
                $traceId,
                $traceId,
                $traceId,
                $spanContext->getParentId(),
                $operationName,
                microtime(true) * 1000000,
                0x01 | 0x02,
                $tags,
                $logs
            );
        }

        if (null !== $tags && ('server' === $tags['span.kind'] ?? null)) {
            return new Span(
                $spanContext->getTraceId(),
                $spanContext->getTraceId(),
                $spanContext->getSpanId(),
                $spanContext->getParentId(),
                $operationName,
                microtime(true) * 1000,
                $spanContext->getFlags(),
                $tags,
                $logs
            );
        }

        $spanId = $this->idGenerator->next();

        return new Span(
            $spanContext->getTraceId(),
            $spanContext->getTraceId(),
            $spanId,
            $spanId,
            $operationName,
            microtime(true) * 1000,
            $spanContext->getFlags(),
            $tags,
            $logs
        );
    }
}
