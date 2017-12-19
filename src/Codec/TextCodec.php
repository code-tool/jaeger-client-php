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
            (int)unpack('J', pack('H*', str_pad($elements[0], 16, '0', STR_PAD_LEFT))),
            (int)unpack('J', pack('H*', str_pad($elements[1], 16, '0', STR_PAD_LEFT))),
            (int)unpack('J', pack('H*', str_pad($elements[2], 16, '0', STR_PAD_LEFT))),
            (int)unpack('J', pack('H*', str_pad($elements[3], 16, '0', STR_PAD_LEFT)))
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
