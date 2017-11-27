<?php

namespace Jaeger\Span\Factory;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\SpanInterface;

interface SpanFactoryInterface
{
    /**
     * @param string           $operationName
     * @param array            $tags
     * @param SpanContext|null $parentContext
     * @param array            $logs
     *
     * @return SpanInterface
     */
    public function create(
        $operationName,
        array $tags = [],
        SpanContext $parentContext = null,
        array $logs = []
    );
}
