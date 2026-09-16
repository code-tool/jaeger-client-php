<?php

declare(strict_types=1);

namespace Jaeger\Tracer;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\SpanInterface;
use Jaeger\Thrift\Tag;

interface TracerInterface extends FinishableInterface
{
    /**
     * @param array<array-key, Tag> $tags
     */
    public function start(string $operationName, array $tags = [], ?SpanContext $context = null): SpanInterface;
}
