<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Process;

class Process extends \CodeTool\OpenTracing\Jaeger\Thrift\Process
{
    public function __construct(string $serviceName, array $tags = [])
    {
        $this->serviceName = $serviceName;
        $this->tags = $tags;
        parent::__construct();
    }
}
