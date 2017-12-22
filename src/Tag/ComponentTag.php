<?php

namespace Jaeger\Tag;

class ComponentTag extends StringTag
{
    /**
     * ComponentTag constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('component', $value);
    }
}
