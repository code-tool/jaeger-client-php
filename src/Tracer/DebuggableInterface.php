<?php

declare(strict_types=1);

namespace Jaeger\Tracer;

use Jaeger\Span\SpanInterface;
use Jaeger\Thrift\Tag;

interface DebuggableInterface
{
    public function enable(string $debugId): DebuggableInterface;

    public function disable(): DebuggableInterface;

    /**
     * @param array<array-key, Tag> $tags
     */
    public function debug(string $operationName, array $tags = []): SpanInterface;
}
