<?php

declare(strict_types=1);

namespace Jaeger\Span\Context;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<string, mixed>
 */
class SpanContext implements IteratorAggregate
{
    /**
     * @param array<string, mixed> $baggage
     */
    public function __construct(
        private int $traceIdHigh,
        private int $traceIdLow,
        private int $spanId,
        private int $parentId,
        private int $flags = 0,
        private array $baggage = [],
    ) {}

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
        return (bool) ($this->flags & 0x01);
    }

    public function isDebug(): bool
    {
        return (bool) ($this->flags & 0x02);
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * @return array<string, mixed>
     */
    public function getBaggage(): array
    {
        return $this->baggage;
    }

    /**
     * @return Traversable<string, mixed>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->baggage);
    }

    public function withItem(string $key, mixed $item): static
    {
        $copy = clone $this;
        $copy->baggage[$key] = $item;

        return $copy;
    }

    public function getItem(string $key, mixed $default = null): mixed
    {
        if (false === \array_key_exists($key, $this->baggage)) {
            return $default;
        }

        return $this->baggage[$key];
    }

    public function withoutItem(string $key): static
    {
        $copy = clone $this;
        unset($copy->baggage[$key]);

        return $copy;
    }
}
