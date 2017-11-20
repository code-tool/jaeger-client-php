<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Log;

use CodeTool\OpenTracing\Jaeger\Thrift\Log;

abstract class AbstractLog extends Log implements LogInterface
{
    public function __construct(int $timestamp, array $fields = [])
    {
        $this->timestamp = $timestamp;
        $this->fields = $fields;
        parent::__construct();
    }
}
