<?php

namespace Jaeger\Tag;

use Jaeger\Thrift\TagType;

class BoolTag extends AbstractTag
{
    /**
     * BoolTag constructor.
     *
     * @param string $key
     * @param bool   $value
     */
    public function __construct($key, $value)
    {
        parent::__construct($key, TagType::BOOL, null, null, $value, null, null);
    }
}
