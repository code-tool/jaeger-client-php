<?php

namespace Jaeger\Tag;

use Jaeger\Thrift\TagType;

class DoubleTag extends AbstractTag
{
    /**
     * DoubleTag constructor.
     *
     * @param string $key
     * @param float  $value
     */
    public function __construct($key, $value)
    {
        parent::__construct($key, TagType::DOUBLE, null, $value, null, null, null);
    }
}
