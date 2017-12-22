<?php

namespace Jaeger\Tag;

class DbInstanceTag extends StringTag
{
    /**
     * DbInstanceTag constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('db.instance', $value);
    }
}
