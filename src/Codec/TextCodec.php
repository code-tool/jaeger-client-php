<?php
declare(strict_types=1);

namespace Jaeger\Codec;

use Jaeger\Span\Context\SpanContext;

class TextCodec implements CodecInterface
{
    public function decode($data): ?SpanContext
    {
        if (false === \is_string($data)) {
            return null;
        }
        $elements = \explode(':', $data);
        if (4 !== \count($elements)) {
            return null;
        }
        [$traceIdHigh, $traceIdLow] = $this->convertInt128($elements[0]);

        return new SpanContext(
            $traceIdHigh,
            $traceIdLow,
            $this->convertInt64($elements[1]),
            $this->convertInt64($elements[2]),
            $this->convertInt64($elements[3])
        );
    }

    public function convertInt64(string $hex): int
    {
        $hex8byte = \str_pad($hex, 16, '0', STR_PAD_LEFT);

        return \unpack('Jint64', pack('H*', $hex8byte))['int64'];
    }

    public function convertInt128(string $hex): array
    {
        $hex16byte = \str_pad($hex, 32, '0', STR_PAD_LEFT);

        return [
            $this->convertInt64(substr($hex16byte, 0, 16)),
            $this->convertInt64(substr($hex16byte, 16, 16)),
        ];
    }

    public function encode(SpanContext $context): string
    {
        return \sprintf(
            '%x%x:%x:%x:%x',
            $context->getTraceIdHigh(),
            $context->getTraceIdLow(),
            $context->getSpanId(),
            $context->getParentId(),
            $context->getFlags()
        );
    }
}
