<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Tracer;

use CodeTool\OpenTracing\Span\Context\SpanContext;

interface ExtractorInterface
{
    public function assign(SpanContext $context): ExtractorInterface;
}
