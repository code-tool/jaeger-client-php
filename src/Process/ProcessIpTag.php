<?php
namespace Jaeger\Process;

use Jaeger\Tag\StringTag;

class ProcessIpTag extends StringTag
{
    public function __construct()
    {
        parent::__construct('ip', $_SERVER['SERVER_ADDR']);
    }
}
