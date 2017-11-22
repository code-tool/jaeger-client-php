<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Tracer;

use CodeTool\OpenTracing\Span\Context\SpanContext;
use CodeTool\OpenTracing\Span\SpanInterface;

interface TracerInterface
{
    public function assign(SpanContext $context): TracerInterface;

    public function flush(): TracerInterface;

    public function getCurrentContext() : ?SpanContext;

    public function start(string $name, array $tags = []): SpanInterface;

    public function finish(SpanInterface $span);
}
