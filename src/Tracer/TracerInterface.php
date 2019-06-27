<?php
declare(strict_types=1);

namespace Jaeger\Tracer;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\SpanInterface;

interface TracerInterface extends FinishableInterface
{
    public function start(string $operationName, array $tags = [], SpanContext $context = null): SpanInterface;
}
