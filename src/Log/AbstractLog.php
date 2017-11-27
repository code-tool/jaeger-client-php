<?php

namespace Jaeger\Log;

abstract class AbstractLog extends \Jaeger\Thrift\Log
{
    public function __construct(array $tags = [])
    {
        $this->timestamp = (int)round(microtime(true) * 1000000);
        $this->fields = $tags;
        parent::__construct();
    }
}
