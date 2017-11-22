<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Process;

class Process extends \CodeTool\OpenTracing\Jaeger\Thrift\Process
{
    public function __construct(string $serviceName)
    {
        $this->serviceName = $serviceName;
        $this->tags = [new ProcessPidTag(), new ProcessSapiTag(), new ProcessUidTag(), new ProcessGidTag()];
        parent::__construct();
    }
}
