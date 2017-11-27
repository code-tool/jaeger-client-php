<?php
declare(strict_types=1);

namespace Jaeger\Tracer;

use Jaeger\Span\Context\SpanContext;

interface InjectableInterface
{
    public function assign(SpanContext $context): InjectableInterface;
}
