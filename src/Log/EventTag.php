<?php

namespace Jaeger\Log;

use Jaeger\Tag\StringTag;

class EventTag extends StringTag
{
    /**
     * EventTag constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('event', $value);
    }
}
