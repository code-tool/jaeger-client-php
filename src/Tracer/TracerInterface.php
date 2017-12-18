<?php

namespace Jaeger\Tracer;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\SpanInterface;

interface TracerInterface
{
    /**
     * @param string $operationName
     * @param array  $tags
     *
     * @return SpanInterface
     */
    public function debug($operationName, array $tags = []);

    /**
     * @param string      $operationName
     * @param array       $tags
     * @param SpanContext $context
     *
     * @return SpanInterface
     */
    public function start($operationName, array $tags = [], SpanContext $context = null);

    /**
     * @param SpanInterface $span
     * @param int           $duration
     *
     * @return mixed
     */
    public function finish(SpanInterface $span, $duration = 0);
}
