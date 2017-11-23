<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Tracer;

use CodeTool\OpenTracing\Span\Context\SpanContext;

interface InjectableInterface
{
    public function assign(SpanContext $context): InjectableInterface;
}
