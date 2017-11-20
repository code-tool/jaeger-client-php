<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Span\Context;

class SpanContext implements \IteratorAggregate
{
    private $traceId;

    private $spanId;

    private $parentId;

    private $debugId;

    private $flags;

    private $baggage;

    public function __construct(
        int $traceId,
        int $spanId,
        int $parentId,
        int $debugId,
        int $flags = 0,
        array $baggage = []
    ) {
        $this->traceId = $traceId;
        $this->spanId = $spanId;
        $this->parentId = $parentId;
        $this->debugId = $debugId;
        $this->flags = $flags;
        $this->baggage = $baggage;
    }

    public function getTraceId(): int
    {
        return $this->traceId;
    }

    public function getSpanId(): int
    {
        return $this->spanId;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function getDebugId(): int
    {
        return $this->debugId;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function isDebugStarting() : bool
    {
        return 0 === $this->traceId && 0 !== $this->debugId;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->baggage);
    }
}
