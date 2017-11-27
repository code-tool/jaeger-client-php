<?php

namespace CodeTool\OpenTracing\General;

use CodeTool\OpenTracing\Tag\StringTag;

class JaegerVersionTag extends StringTag
{
    public function __construct()
    {
        parent::__construct('jaeger.version', 'PHP');
    }
}
