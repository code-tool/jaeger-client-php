<?php

namespace CodeTool\OpenTracing\Tracer;

use CodeTool\OpenTracing\Span\Context\SpanContext;

interface InjectableInterface
{
    /**
     * @param SpanContext $context
     *
     * @return InjectableInterface
     */
    public function assign(SpanContext $context);
}
