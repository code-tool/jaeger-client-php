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
     * @param string $hex
     *
     * @return int
     */
    public function convertInt64($hex)
    {
        $hex8byte = str_pad($hex, 16, '0', STR_PAD_LEFT);

        return unpack('Jint64', pack('H*', $hex8byte))['int64'];
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
            $this->convertInt64($elements[0]),
            $this->convertInt64($elements[1]),
            $this->convertInt64($elements[2]),
            $this->convertInt64($elements[3])
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
