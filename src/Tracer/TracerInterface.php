<?php

namespace Jaeger\Tracer;

use Jaeger\Span\SpanInterface;

interface TracerInterface
{
    /**
     * @param string $name
     * @param array  $tags
     *
     * @return SpanInterface
     */
    public function start($name, array $tags = []);

    public function finish(SpanInterface $span);
}
