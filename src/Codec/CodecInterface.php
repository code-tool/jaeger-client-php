<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Codec;

use CodeTool\OpenTracing\Span\Context\SpanContext;

interface CodecInterface
{
    public function decode($data) : ?SpanContext;

    public function encode(SpanContext $context);
}
