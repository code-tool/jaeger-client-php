<?php

namespace Jaeger\Span\Context;

class SpanContext implements \IteratorAggregate
{
    private $traceId;

    private $spanId;

    private $parentId;

    private $flags;

    private $baggage;

    /**
     * SpanContext constructor.
     *
     * @param int   $traceId
     * @param int   $spanId
     * @param int   $parentId
     * @param int   $flags
     * @param array $baggage
     */
    public function __construct(
        $traceId,
        $spanId,
        $parentId,
        $flags = 0,
        array $baggage = []
    ) {
        $this->traceId = $traceId;
        $this->spanId = $spanId;
        $this->parentId = $parentId;
        $this->flags = $flags;
        $this->baggage = $baggage;
    }

    public function getTraceId()
    {
        return $this->traceId;
    }

    public function getSpanId()
    {
        return $this->spanId;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function isDebug()
    {
        return (bool)($this->flags & 0x02);
    }

    public function getFlags()
    {
        return $this->flags;
    }

    public function getBaggage()
    {
        return $this->baggage;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->baggage);
    }

    /**
     * @param string $key
     * @param mixed  $item
     *
     * @return SpanContext
     */
    public function withItem($key, $item)
    {
        $copy = clone $this;
        $copy->baggage[$key] = $item;

        return $copy;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getItem($key, $default = null)
    {
        if (false === array_key_exists($key, $this->baggage)) {
            return $default;
        }

        return $this->baggage[$key];
    }

    /**
     * @param string $key
     *
     * @return SpanContext
     */
    public function withoutItem($key)
    {
        $copy = clone $this;
        unset($copy->baggage[$key]);

        return $copy;
    }
}
