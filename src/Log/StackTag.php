<?php

namespace Jaeger\Log;

use Jaeger\Tag\StringTag;

class StackTag extends StringTag
{
    /**
     * StackTag constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('stack', $value);
    }
}
