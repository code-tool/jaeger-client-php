<?php
declare(strict_types=1);

namespace Jaeger\Log;

use Jaeger\Tag\StringTag;

class ErrorObjectTag extends StringTag
{
    public function __construct(\JsonSerializable $value)
    {
        parent::__construct('error.object', json_encode($value));
    }
}
