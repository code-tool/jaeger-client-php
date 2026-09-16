<?php

declare(strict_types=1);

namespace Jaeger\Codec;

use InvalidArgumentException;
use Jaeger\Span\Context\SpanContext;

class TextCodec implements CodecInterface
{
    public function decode(mixed $data): ?SpanContext
    {
        if (!\is_string($data)) {
            return null;
        }

        $elements = explode(':', $data);
        if (4 !== \count($elements)) {
            return null;
        }

        [$traceIdHigh, $traceIdLow] = $this->convertInt128($elements[0]);

        return new SpanContext(
            $traceIdHigh,
            $traceIdLow,
            $this->convertInt64($elements[1]),
            $this->convertInt64($elements[2]),
            $this->convertInt64($elements[3]),
        );
    }

    public function convertInt64(string $hex): int
    {
        $hex8byte = str_pad($hex, 16, '0', STR_PAD_LEFT);
        $binary = pack('H*', $hex8byte);
        $unpacked = unpack('Jint64', $binary);

        if (false === $unpacked) {
            throw new InvalidArgumentException(\sprintf('Cannot unpack "%s" as a 64-bit integer', $hex));
        }

        return (int) $unpacked['int64'];
    }

    /**
     * @return array{int, int}
     */
    public function convertInt128(string $hex): array
    {
        $hex16byte = str_pad($hex, 32, '0', STR_PAD_LEFT);

        return [
            $this->convertInt64(substr($hex16byte, 0, 16)),
            $this->convertInt64(substr($hex16byte, 16, 16)),
        ];
    }

    public function encode(SpanContext $context): string
    {
        return \sprintf(
            '%s:%x:%x:%x',
            $this->encodeTraceId($context->getTraceIdHigh(), $context->getTraceIdLow()),
            $context->getSpanId(),
            $context->getParentId(),
            $context->getFlags(),
        );
    }

    /**
     * A 128-bit trace id pads its low half to a full 16 hex digits, otherwise decode() cannot tell
     * where the high half ends. A 64-bit trace id is written as-is, with no leading zero.
     */
    private function encodeTraceId(int $traceIdHigh, int $traceIdLow): string
    {
        if (0 === $traceIdHigh) {
            return \sprintf('%x', $traceIdLow);
        }

        return \sprintf('%x%016x', $traceIdHigh, $traceIdLow);
    }
}
