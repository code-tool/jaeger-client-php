<?php
declare(strict_types=1);

namespace Jaeger\Span\Factory;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Span\SpanInterface;

interface SpanFactoryInterface
{
    public function parent(
        string $operationName,
        bool $isDebug = false,
        array $tags = [],
        array $logs = []
    ): SpanInterface;

    public function child(
        string $operationName,
        SpanContext $parentContext,
        array $tags = [],
        array $logs = []
    ): SpanInterface;
}
