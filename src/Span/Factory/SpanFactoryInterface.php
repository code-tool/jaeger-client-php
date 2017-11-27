<?php
declare(strict_types=1);

namespace Jaeger\Span\Factory;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\SpanInterface;

interface SpanFactoryInterface
{
    public function create(
        string $operationName,
        array $tags = [],
        SpanContext $parentContext = null,
        array $logs = []
    ): SpanInterface;
}
