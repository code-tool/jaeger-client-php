<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Span;

use CodeTool\OpenTracing\Jaeger\Thrift\Log;
use CodeTool\OpenTracing\Jaeger\Thrift\Tag;
use CodeTool\OpenTracing\Span\Context\SpanContext;

interface SpanInterface
{
    public function getContext(): SpanContext;

    public function finish();

    public function addTag(Tag $tag): SpanInterface;

    public function addLog(Log $log): SpanInterface;

    public function withItem(string $key, $item): SpanInterface;

    public function getItem(string $key, $default = null);

    public function withoutItem(string $key): SpanInterface;
}

