<?php
declare(strict_types=1);

namespace Jaeger\Span\Factory;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\SpanInterface;
use Jaeger\Tracer\TracerInterface;

interface SpanFactoryInterface
{
    public function parent(
        TracerInterface $tracer,
        string $operationName,
        string $debugId,
        array $tags = [],
        array $logs = []
    ): SpanInterface;

    public function child(
        TracerInterface $tracer,
        string $operationName,
        SpanContext $parentContext,
        array $tags = [],
        array $logs = []
    ): SpanInterface;
}
