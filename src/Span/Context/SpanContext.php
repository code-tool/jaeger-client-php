<?php
declare(strict_types=1);

namespace Jaeger\Span\Context;

class SpanContext implements \IteratorAggregate
{
    private int $traceIdHigh;

    private int $traceIdLow;

    private int $spanId;

    private int $parentId;

    private int $flags;

    private array $baggage;

    public function __construct(
        int   $traceIdHigh,
        int   $traceIdLow,
        int   $spanId,
        int   $parentId,
        int   $flags = 0,
        array $baggage = []
    ) {
        $this->traceIdHigh = $traceIdHigh;
        $this->traceIdLow = $traceIdLow;
        $this->spanId = $spanId;
        $this->parentId = $parentId;
        $this->flags = $flags;
        $this->baggage = $baggage;
    }

    public function getTraceId(): int
    {
        return $this->traceIdLow;
    }

    public function getTraceIdHigh(): int
    {
        return $this->traceIdHigh;
    }

    public function getTraceIdLow(): int
    {
        return $this->traceIdLow;
    }

    public function getSpanId(): int
    {
        return $this->spanId;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function isSampled(): bool
    {
        return (bool)($this->flags & 0x01);
    }

    public function isDebug(): bool
    {
        return (bool)($this->flags & 0x02);
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function getBaggage(): array
    {
        return $this->baggage;
    }

    /**
     * @return \Traversable
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator($this->baggage);
    }

    public function withItem(string $key, $item)
    {
        $copy = clone $this;
        $copy->baggage[$key] = $item;

        return $copy;
    }

    public function getItem(string $key, $default = null)
    {
        if (false === array_key_exists($key, $this->baggage)) {
            return $default;
        }

        return $this->baggage[$key];
    }

    public function withoutItem(string $key)
    {
        $copy = clone $this;
        unset($copy->baggage[$key]);

        return $copy;
    }
}
