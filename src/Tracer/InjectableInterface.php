<?php

namespace Jaeger\Tracer;

use Jaeger\Span\Context\SpanContext;

interface InjectableInterface
{
    /**
     * @param SpanContext $context
     *
     * @return InjectableInterface
     */
    public function assign(SpanContext $context);

    /**
     * @param SpanContext $context
     *
     * @return InjectableInterface
     */
    public function remove(SpanContext $context);
}
