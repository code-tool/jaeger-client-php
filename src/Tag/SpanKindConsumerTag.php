<?php
declare(strict_types=1);

namespace Jaeger\Tag;

class SpanKindConsumerTag extends AbstractSpanKindTag
{
    public function __construct()
    {
        parent::__construct('consumer');
    }
}
