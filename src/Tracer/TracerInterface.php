<?php
declare(strict_types=1);

namespace Jaeger\Tracer;

use Jaeger\Span\SpanInterface;

interface TracerInterface
{
    public function start(string $name, array $tags = []): SpanInterface;

    public function finish(SpanInterface $span);
}
