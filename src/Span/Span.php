<?php

namespace Jaeger\Span;

use Jaeger\Span\Context\SpanContext;
use Jaeger\Tag\ErrorTag;
use Jaeger\Tag\OutOfScopeTag;
use Jaeger\Thrift\Log;
use Jaeger\Thrift\Tag;
use Jaeger\Tracer\TracerInterface;

class Span extends \Jaeger\Thrift\Span implements SpanInterface
{
    private $tracer;

    private $context;

    /**
     * Span constructor.
     *
     * @param TracerInterface $tracer
     * @param SpanContext     $context
     * @param string          $operationName
     * @param int             $startTime
     * @param array           $tags
     * @param array           $logs
     */
    public function __construct(
        TracerInterface $tracer,
        SpanContext $context,
        $operationName,
        $startTime,
        array $tags = [],
        array $logs = []
    ) {
        $this->tracer = $tracer;
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

    public function __destruct()
    {
        if (null !== $this->duration) {
            return $this;
        }
        $this->tags[] = new ErrorTag();
        $this->tags[] = new OutOfScopeTag();

        $this->tracer->finish($this);

        return $this;
    }

    /**
     * @return SpanContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return bool
     */
    public function isSampled()
    {
        return (bool)($this->flags & 1);
    }

    /**
     * @param int $startTimeUsec
     *
     * @return SpanInterface
     */
    public function start($startTimeUsec)
    {
        $this->startTime = $startTimeUsec;

        return $this;
    }

    /**
     * @param int $durationUsec
     *
     * @return SpanInterface
     */
    public function finish($durationUsec = 0)
    {
        if (0 === $durationUsec) {
            $durationUsec = (int)(microtime(true) * 1000000) - $this->startTime;
        }
        $this->duration = $durationUsec;

        return $this;
    }

    /**
     * @param Tag $tag
     *
     * @return SpanInterface
     */
    public function addTag(Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * @param Log $log
     *
     * @return SpanInterface
     */
    public function addLog(Log $log)
    {
        $this->logs[] = $log;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $item
     *
     * @return SpanInterface
     */
    public function withItem($key, $item)
    {
        $this->context = $this->context->withItem($key, $item);

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getItem($key, $default = null)
    {
        return $this->context->getItem($key, $default);
    }

    /**
     * @param string $key
     *
     * @return SpanInterface
     */
    public function withoutItem($key)
    {
        $this->context = $this->context->withoutItem($key);

        return $this;
    }
}
