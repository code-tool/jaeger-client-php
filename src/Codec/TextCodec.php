<?php

namespace Jaeger\Codec;

use Jaeger\Span\Context\SpanContext;

class TextCodec implements CodecInterface
{
    public function getName()
    {
        return 'text';
    }

    /**
     * @param $data
     *
     * @return SpanContext|null
     */
    public function decode($data)
    {
        if (false === is_string($data)) {
            return null;
        }
        $elements = explode(':', $data);
        if (4 !== count($elements)) {
            return null;
        }

        return new SpanContext(
            hexdec($elements[0]),
            hexdec($elements[1]),
            hexdec($elements[2]),
            0,
            hexdec($elements[3])
        );
    }

    /**
     * @param SpanContext $context
     *
     * @return string
     */
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
