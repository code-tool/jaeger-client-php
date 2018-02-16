<?php

namespace Jaeger\Log;

use Jaeger\Thrift\Log;

abstract class AbstractLog extends Log
{
    public function __construct(array $tags = [], $timestamp = 0)
    {
        $this->timestamp = $timestamp ? $timestamp : (int)round(microtime(true) * 1000000);
        $this->fields = $tags;
        parent::__construct();
    }
}
