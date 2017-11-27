<?php
declare(strict_types=1);

namespace Jaeger\General;

use Jaeger\Tag\StringTag;

class JaegerHostnameTag extends StringTag
{
    public function __construct()
    {
        parent::__construct('jaeger.hostname', gethostname());
    }
}
