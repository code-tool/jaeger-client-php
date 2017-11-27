<?php
declare(strict_types=1);

namespace Jaeger\General;

use Jaeger\Tag\StringTag;

class PhpBinaryTag extends StringTag
{
    public function __construct()
    {
        parent::__construct('php.bin', PHP_BINARY);
    }
}
