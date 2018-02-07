<?php
declare(strict_types=1);

namespace Jaeger\Process;

use Jaeger\Tag\StringTag;

class ProcessIpTag extends StringTag
{
    private const IP_DEFAULT = '127.0.0.1';
    
    public function __construct()
    {
        parent::__construct('ip', $_SERVER['SERVER_ADDR'] ?? self::IP_DEFAULT);
    }
}
