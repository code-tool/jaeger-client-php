<?php
declare(strict_types=1);

namespace Jaeger\Span\Context;

interface ContextAwareInterface
{
    public function getContext(): ?SpanContext;
}
