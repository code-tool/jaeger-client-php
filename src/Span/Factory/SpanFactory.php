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

    public function createContext(SpanContext $parentContext = null): SpanContext
    {
        if (null === $parentContext) {
            return new SpanContext(
                $this->idGenerator->next(),
                $this->idGenerator->next(),
                0,
                0,
                0x01,
                []
            );
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
            $this->createContext($parentContext),
            $operationName,
            (int)round(microtime(true) * 1000000),
            $tags,
            $logs
        );
    }
}
