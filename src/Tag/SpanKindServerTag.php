<?php
namespace Jaeger\Tag;

class SpanKindServerTag extends AbstractSpanKindTag
{
    public function __construct()
    {
        parent::__construct('server');
    }
}
