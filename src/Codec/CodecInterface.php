<?php

namespace Jaeger\Codec;

use Jaeger\Span\Context\SpanContext;

interface CodecInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param $data
     *
     * @return SpanContext|null
     */
    public function decode($data);

    /**
     * @param SpanContext $context
     *
     * @return mixed
     */
    public function encode(SpanContext $context);
}
