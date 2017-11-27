<?php

namespace CodeTool\OpenTracing\Codec;

use CodeTool\OpenTracing\Span\Context\SpanContext;

interface CodecInterface
{
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
