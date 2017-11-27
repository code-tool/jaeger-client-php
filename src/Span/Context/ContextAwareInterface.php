<?php

namespace CodeTool\OpenTracing\Span\Context;

interface ContextAwareInterface
{
    /**
     * @return SpanContext|null
     */
    public function getContext();
}
