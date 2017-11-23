<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Tracer;

use CodeTool\OpenTracing\Span\Context\SpanContext;

interface InjectorInterface
{
    public function getContext() : ?SpanContext;
}
