<?php
declare(strict_types=1);

namespace Jaeger\Span;

use Jaeger\Thrift\Log;
use Jaeger\Thrift\Tag;
use Jaeger\Span\Context\SpanContext;

class Span extends \Jaeger\Thrift\Span implements SpanInterface
{
    private $context;

    public function __construct(
        SpanContext $context,
        string $operationName,
        int $startTime,
        array $tags = [],
        array $logs = []
    ) {
        $this->context = $context;
        $this->traceIdLow = $context->getTraceId();
        $this->traceIdHigh = 0;
        $this->spanId = $context->getSpanId();
        $this->parentSpanId = $context->getParentId();
        $this->flags = $context->getFlags();
        $this->operationName = $operationName;
        $this->startTime = $startTime;

        $this->tags = $tags;
        $this->logs = $logs;
        parent::__construct();
    }

    public function getContext(): ?SpanContext
    {
        return $this->context;
    }

    public function finish(int $duration = 0): SpanInterface
    {
        if (0 === $duration) {
            $duration = round(microtime(true) * 1000000) - $this->startTime;
        }
        $this->duration = $duration;

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

    public function withItem(string $key, $item): SpanInterface
    {
        $this->context = $this->context->withItem($key, $item);

        return $this;
    }

    public function getItem(string $key, $default = null)
    {
        return $this->context->getItem($key, $default);
    }

    public function withoutItem(string $key): SpanInterface
    {
        $this->context = $this->context->withoutItem($key);

        return $this;
    }
}
