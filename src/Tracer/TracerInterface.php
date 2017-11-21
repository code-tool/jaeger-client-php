<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Tracer;

use CodeTool\OpenTracing\Span\SpanInterface;

interface TracerInterface
{
    public function onStart() : TracerInterface;

    public function onFinish() : TracerInterface;

    public function start(string $name, array $tags = []) : SpanInterface;

    public function finish(SpanInterface $span);
}
