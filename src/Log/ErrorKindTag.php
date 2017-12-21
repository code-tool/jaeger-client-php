<?php
declare(strict_types=1);

namespace Jaeger\Log;

use Jaeger\Tag\StringTag;

class ErrorKindTag extends StringTag
{
    public function __construct(string $value)
    {
        parent::__construct('error.kind', $value);
    }
}
