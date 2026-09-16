<?php

declare(strict_types=1);

namespace Jaeger\Span;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Tag\ErrorTag;
use Jaeger\Tag\OutOfScopeTag;
use Jaeger\Thrift\Log;
use Jaeger\Thrift\Tag;
use Jaeger\Tracer\FinishableInterface;

class Span extends \Jaeger\Thrift\Span implements SpanInterface
{
    /**
     * @param array<array-key, Tag> $tags
     * @param array<array-key, Log> $logs
     */
    public function __construct(
        private readonly FinishableInterface $tracer,
        private SpanContext $context,
        string $operationName,
        int $startTime,
        array $tags = [],
        array $logs = [],
    ) {
        $this->traceIdLow = $this->context->getTraceIdLow();
        $this->traceIdHigh = $this->context->getTraceIdHigh();
        $this->spanId = $this->context->getSpanId();
        $this->parentSpanId = $this->context->getParentId();
        $this->flags = $this->context->getFlags();
        $this->operationName = $operationName;
        $this->startTime = $startTime;
        $this->tags = $tags;
        $this->logs = $logs;
        parent::__construct();
    }

    public function __destruct()
    {
        if (null !== $this->duration) {
            return;
        }

        $this->tags[] = new ErrorTag();
        $this->tags[] = new OutOfScopeTag();
        $this->tracer->finish($this);
    }

    public function getContext(): ?SpanContext
    {
        return $this->context;
    }

    public function isSampled(): bool
    {
        return $this->context->isSampled();
    }

    public function start(int $startTimeUsec): SpanInterface
    {
        $this->startTime = $startTimeUsec;

        return $this;
    }

    public function finish(int $durationUsec = 0): SpanInterface
    {
        $this->duration = 0 !== $durationUsec
            ? $durationUsec
            : (int) (microtime(true) * 1000000.0) - (int) $this->startTime;
        $this->tracer->finish($this, -1);

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

    public function withItem(string $key, mixed $item): SpanInterface
    {
        $this->context = $this->context->withItem($key, $item);

        return $this;
    }

    public function getItem(string $key, mixed $default = null): mixed
    {
        return $this->context->getItem($key, $default);
    }

    public function withoutItem(string $key): SpanInterface
    {
        $this->context = $this->context->withoutItem($key);

        return $this;
    }
}
