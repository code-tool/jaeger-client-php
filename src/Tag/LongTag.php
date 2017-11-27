<?php

namespace CodeTool\OpenTracing\Tag;

use CodeTool\OpenTracing\Jaeger\Thrift\TagType;

class LongTag extends AbstractTag
{
    /**
     * LongTag constructor.
     *
     * @param string $key
     * @param int    $value
     */
    public function __construct($key, $value)
    {
        parent::__construct($key, TagType::LONG, null, null, null, $value, null);
    }
}
