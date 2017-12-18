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
            (int)hexdec($elements[0]),
            (int)hexdec($elements[1]),
            (int)hexdec($elements[2]),
            (int)hexdec($elements[3])
        );
    }

    public function encode(SpanContext $context)
    {
        return sprintf(
            '%x:%x:%x:%x',
            $context->getTraceId(),
            $context->getSpanId(),
            $context->getParentId(),
            $context->getFlags()
        );
    }
}
