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
    private FinishableInterface $tracer;

    private SpanContext $context;

    public function __construct(
        FinishableInterface $tracer,
        SpanContext         $context,
        string              $operationName,
        int                 $startTime,
        array               $tags = [],
        array               $logs = []
    ) {
        $this->tracer = $tracer;
        $this->context = $context;
        $this->traceIdLow = $context->getTraceIdLow();
        $this->traceIdHigh = $context->getTraceIdHigh();
        $this->spanId = $context->getSpanId();
        $this->parentSpanId = $context->getParentId();
        $this->flags = $context->getFlags();
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
        $this->duration = $durationUsec ?: (microtime(true) * 1000000) - $this->startTime;
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
