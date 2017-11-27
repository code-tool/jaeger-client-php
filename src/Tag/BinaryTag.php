<?php

namespace Jaeger\Tag;

use Jaeger\Thrift\TagType;

class BinaryTag extends AbstractTag
{
    /**
     * BinaryTag constructor.
     *
     * @param string $key
     * @param string $value
     */
    public function __construct($key, $value)
    {
        parent::__construct($key, TagType::BINARY, null, null, null, null, $value);
    }
}
