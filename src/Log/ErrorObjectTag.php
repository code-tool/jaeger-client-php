<?php
namespace Jaeger\Log;

use Jaeger\Tag\StringTag;

class ErrorObjectTag extends StringTag
{
    /**
     * ErrorObjectTag constructor.
     *
     * @param \JsonSerializable $value
     */
    public function __construct(\JsonSerializable $value)
    {
        parent::__construct('error.object', json_encode($value));
    }
}
