<?php

declare(strict_types=1);

namespace Jaeger\Codec;

use Jaeger\Span\Context\SpanContext;

interface CodecInterface
{
    public function decode(mixed $data): ?SpanContext;

    public function encode(SpanContext $context): string;
}
