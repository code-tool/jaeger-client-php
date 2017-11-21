<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Log;

class Log extends \CodeTool\OpenTracing\Jaeger\Thrift\Log implements LogInterface
{
    public function __construct(int $timestamp, array $fields = [])
    {
        $this->timestamp = $timestamp;
        $this->fields = $fields;
        parent::__construct();
    }
}
