<?php
declare(strict_types=1);

namespace Jaeger\Tracer;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\SpanInterface;

interface TracerInterface
{
    public function start(string $operationName, array $tags = [], SpanContext $context = null): SpanInterface;

    public function finish(SpanInterface $span, int $duration = 0);
}
