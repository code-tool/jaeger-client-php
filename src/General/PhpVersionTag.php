<?php
declare(strict_types=1);

namespace Jaeger\General;

use Jaeger\Tag\StringTag;

class PhpVersionTag extends StringTag
{
    public function __construct()
    {
        parent::__construct('php.version', PHP_VERSION);
    }
}
