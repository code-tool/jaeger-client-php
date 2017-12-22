<?php

namespace Jaeger\Tag;

class SpanKindProducerTag extends AbstractSpanKindTag
{
    public function __construct()
    {
        parent::__construct('producer');
    }
}
