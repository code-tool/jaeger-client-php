<?php

namespace CodeTool\OpenTracing\General;

use CodeTool\OpenTracing\Tag\StringTag;

class JaegerHostnameTag extends StringTag
{
    public function __construct()
    {
        parent::__construct('jaeger.hostname', gethostname());
    }
}
