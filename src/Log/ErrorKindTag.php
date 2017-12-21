<?php
namespace Jaeger\Log;

use Jaeger\Tag\StringTag;

class ErrorKindTag extends StringTag
{
    /**
     * ErrorKindTag constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('error.kind', $value);
    }
}
