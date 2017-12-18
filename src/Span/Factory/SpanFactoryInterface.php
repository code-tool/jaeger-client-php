<?php

namespace Jaeger\Span\Factory;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\SpanInterface;

interface SpanFactoryInterface
{
    /**
     * @param string $operationName
     * @param bool   $isDebug
     * @param array  $tags
     * @param array  $logs
     *
     * @return SpanInterface
     */
    public function parent(
        $operationName,
        $isDebug = false,
        array $tags = [],
        array $logs = []
    );

    /**
     * @param string      $operationName
     * @param SpanContext $parentContext
     * @param array       $tags
     * @param array       $logs
     *
     * @return SpanInterface
     */
    public function child(
        $operationName,
        SpanContext $parentContext,
        array $tags = [],
        array $logs = []
    );
}
