<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Span\Context;

interface ContextAwareInterface
{
    public function getContext(): ?SpanContext;
}
