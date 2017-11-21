<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Span\Factory;

use CodeTool\OpenTracing\Span\Context\SpanContext;
use CodeTool\OpenTracing\Span\SpanInterface;

interface SpanFactoryInterface
{
    public function create(
        SpanContext $parentContext,
        string $operationName,
        array $tags = [],
        array $logs = []
    ): SpanInterface;
}
