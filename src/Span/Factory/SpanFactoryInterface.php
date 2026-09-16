<?php

declare(strict_types=1);

namespace Jaeger\Span\Factory;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\SpanInterface;
use Jaeger\Thrift\Log;
use Jaeger\Thrift\Tag;
use Jaeger\Tracer\TracerInterface;

interface SpanFactoryInterface
{
    /**
     * @param array<array-key, Tag> $tags
     * @param array<array-key, Log> $logs
     */
    public function parent(
        TracerInterface $tracer,
        string $operationName,
        string $debugId,
        array $tags = [],
        array $logs = [],
    ): SpanInterface;

    /**
     * @param array<array-key, Tag> $tags
     * @param array<array-key, Log> $logs
     */
    public function child(
        TracerInterface $tracer,
        string $operationName,
        SpanContext $parentContext,
        array $tags = [],
        array $logs = [],
    ): SpanInterface;
}
