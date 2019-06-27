<?php
declare(strict_types=1);

namespace Jaeger\Span;

interface SpanAwareInterface
{
    public function getSpan(): ?SpanInterface;
}
