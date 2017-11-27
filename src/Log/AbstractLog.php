<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Log;

abstract class AbstractLog extends \CodeTool\OpenTracing\Jaeger\Thrift\Log
{
    public function __construct(array $tags = [])
    {
        $this->timestamp = (int)round(microtime(true) * 1000000);
        $this->fields = $tags;
        parent::__construct();
    }
}
