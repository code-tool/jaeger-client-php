<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\General;

use CodeTool\OpenTracing\Tag\StringTag;

class PhpBinaryTag extends StringTag
{
    public function __construct()
    {
        parent::__construct('php.bin', PHP_BINARY);
    }
}
