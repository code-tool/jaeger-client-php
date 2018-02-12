<?php
namespace Jaeger\Tag;

class DebugRequestTag extends StringTag
{
    /**
     * DebugRequestTag constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('debug.request', $value);
    }
}
