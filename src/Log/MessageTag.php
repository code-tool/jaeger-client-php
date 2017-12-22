<?php

namespace Jaeger\Log;

use Jaeger\Tag\StringTag;

class MessageTag extends StringTag
{
    /**
     * MessageTag constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('message', $value);
    }
}
