<?php
declare(strict_types=1);

namespace Jaeger\Codec;

use Jaeger\Span\Context\SpanContext;

class TextCodec implements CodecInterface
{
    public function decode($data): ?SpanContext
    {
        if (false === is_string($data)) {
            return null;
        }
        $elements = explode(':', $data);
        if (4 !== count($elements)) {
            return null;
        }

        return new SpanContext(
            $elements[0],
            $this->convertInt64($elements[1]),
            $this->convertInt64($elements[2]),
            $this->convertInt64($elements[3])
        );
    }

    public function convertInt64(string $hex): int
    {
        $hex8byte = str_pad($hex, 16, '0', STR_PAD_LEFT);

        return unpack('Jint64', pack('H*', $hex8byte))['int64'];
    }

    public function encode(SpanContext $context)
    {
        return sprintf(
            '%s:%x:%x:%x',
            $context->getTraceId(),
            $context->getSpanId(),
            $context->getParentId(),
            $context->getFlags()
        );
    }
}
