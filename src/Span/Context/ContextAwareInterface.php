<?php

namespace Jaeger\Span\Context;

interface ContextAwareInterface
{
    /**
     * @return SpanContext|null
     */
    public function getContext();
}
