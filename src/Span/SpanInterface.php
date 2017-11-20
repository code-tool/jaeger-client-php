<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Span;

use CodeTool\OpenTracing\Jaeger\Thrift\Log;
use CodeTool\OpenTracing\Jaeger\Thrift\Tag;

interface SpanInterface
{
    public function getOperationName(): string;

    public function getTraceId(): int;

    public function getId(): int;

    public function getParentId(): int;

    public function getFlags() : int;

    public function finish();

    public function addTag(Tag $tag): SpanInterface;

    public function addLog(Log $log): SpanInterface;
}
