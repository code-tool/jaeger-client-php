<?php
declare(strict_types=1);

namespace Jaeger\Tracer;

use Jaeger\Span\SpanInterface;

interface DebuggableInterface
{
    public function enable(string $requestId): DebuggableInterface;

    public function disable(): DebuggableInterface;

    public function debug(string $operationName, array $tags = []): SpanInterface;
}
