<?php

namespace Jaeger\Tracer;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\SpanInterface;

interface TracerInterface
{
    /**
     * @param string      $name
     * @param array       $tags
     * @param SpanContext $context
     *
     * @return SpanInterface
     */
    public function start($name, array $tags = [], SpanContext $context = null);

    /**
     * @param SpanInterface $span
     * @param int           $duration
     *
     * @return mixed
     */
    public function finish(SpanInterface $span, $duration = 0);
}
