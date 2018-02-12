<?php

namespace Jaeger\Span\Factory;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\SpanInterface;
use Jaeger\Tracer\TracerInterface;

interface SpanFactoryInterface
{
    /**
     * @param TracerInterface $tracer
     * @param string $operationName
     * @param bool   $isDebug
     * @param array  $tags
     * @param array  $logs
     *
     * @return SpanInterface
     */
    public function parent(
        TracerInterface $tracer,
        $operationName,
        $debugId,
        $operationName,
        $debugId,
        array $tags = [],
        array $logs = []
    );

    /**
     * @param TracerInterface $tracer
     * @param string      $operationName
     * @param SpanContext $parentContext
     * @param array       $tags
     * @param array       $logs
     *
     * @return SpanInterface
     */
    public function child(
        TracerInterface $tracer,
        $operationName,
        $operationName,
        SpanContext $parentContext,
        array $tags = [],
        array $logs = []
    );
}
