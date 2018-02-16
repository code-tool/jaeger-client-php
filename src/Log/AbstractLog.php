<?php
declare(strict_types=1);

namespace Jaeger\Log;

abstract class AbstractLog extends \Jaeger\Thrift\Log
{
    public function __construct(array $tags = [], int $timestamp = 0)
    {
        $this->timestamp = 0 !== $timestamp ? $timestamp : (int)round(microtime(true) * 1000000);
        $this->fields = $tags;
        parent::__construct();
    }
}
