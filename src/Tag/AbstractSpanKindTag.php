<?php

namespace Jaeger\Tag;

abstract class AbstractSpanKindTag extends StringTag
{
    /**
     * AbstractSpanKindTag constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('span.kind', $value);
    }
}
