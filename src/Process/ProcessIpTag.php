<?php
namespace Jaeger\Process;

use Jaeger\Tag\StringTag;

class ProcessIpTag extends StringTag
{
    public function __construct()
    {
        parent::__construct('ip', isset($_SERVER['SERVER_ADDR']) ?  $_SERVER['SERVER_ADDR']: '127.0.0.1');
    }
}
