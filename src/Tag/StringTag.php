<?php

namespace Jaeger\Tag;

use Jaeger\Thrift\TagType;

class StringTag extends AbstractTag
{
    /**
     * StringTag constructor.
     *
     * @param string $key
     * @param int    $value
     */
    public function __construct($key, $value)
    {
        parent::__construct($key, TagType::STRING, $value, null, null, null, null);
    }
}
