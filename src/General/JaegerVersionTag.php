<?php

namespace Jaeger\General;

use Jaeger\Tag\StringTag;

class JaegerVersionTag extends StringTag
{
    public function __construct()
    {
        parent::__construct('jaeger.version', 'PHP');
    }
}
