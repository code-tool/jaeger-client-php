<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Span;

use CodeTool\OpenTracing\Jaeger\Thrift\Log;
use CodeTool\OpenTracing\Jaeger\Thrift\Tag;

class Span extends \CodeTool\OpenTracing\Jaeger\Thrift\Span implements SpanInterface
{
    public function __construct(
        int $traceIdHigh,
        int $traceIdLow,
        int $spanId,
        int $parentSpanId,
        string $operationName,
        int $startTime,
        int $flags = 0,
        array $tags = [],
        array $logs = []
    ) {
        $this->traceIdHigh = $traceIdHigh;
        $this->traceIdLow = $traceIdLow;
        $this->spanId = $spanId;
        $this->parentSpanId = $parentSpanId;
        $this->operationName = $operationName;
        $this->startTime = $startTime;
        $this->flags = $flags;
        $this->tags = $tags;
        $this->logs = $logs;
        parent::__construct();
    }

    public function getTraceId(): int
    {
        return $this->traceIdLow + $this->traceIdHigh;
    }

    public function getId(): int
    {
        return $this->spanId;
    }

    public function getParentId(): int
    {
        return $this->parentSpanId;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function getOperationName(): string
    {
        return $this->operationName;
    }

    public function finish()
    {
        $this->duration = round(microtime(true) * 1000000) - $this->startTime;

        return $this;
    }


    public function addTag(Tag $tag): SpanInterface
    {
        $this->tags[] = $tag;

        return $this;
    }

    public function addLog(Log $log): SpanInterface
    {
        $this->logs[] = $log;

        return $this;
    }
}
