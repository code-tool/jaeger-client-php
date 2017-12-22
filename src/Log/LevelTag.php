<?php

namespace Jaeger\Log;

use Jaeger\Tag\StringTag;

class LevelTag extends StringTag
{
    /**
     * LevelTag constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('level', $value);
    }
}
