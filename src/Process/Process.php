<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Process;

use CodeTool\OpenTracing\General\JaegerHostnameTag;
use CodeTool\OpenTracing\General\JaegerVersionTag;
use CodeTool\OpenTracing\General\PhpBinaryTag;
use CodeTool\OpenTracing\General\PhpVersionTag;

class Process extends \CodeTool\OpenTracing\Jaeger\Thrift\Process
{
    public function __construct(string $serviceName)
    {
        $this->serviceName = $serviceName;
        $this->tags = [
            new JaegerVersionTag(),
            new JaegerHostnameTag(),
            new PhpBinaryTag(),
            new PhpVersionTag(),
            new ProcessPidTag(),
            new ProcessSapiTag(),
            new ProcessUidTag(),
            new ProcessGidTag()
        ];
        parent::__construct();
    }
}
