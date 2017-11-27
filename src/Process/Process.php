<?php

namespace CodeTool\OpenTracing\Process;

use CodeTool\OpenTracing\General\JaegerHostnameTag;
use CodeTool\OpenTracing\General\JaegerVersionTag;
use CodeTool\OpenTracing\General\PhpBinaryTag;
use CodeTool\OpenTracing\General\PhpVersionTag;

class Process extends \CodeTool\OpenTracing\Jaeger\Thrift\Process
{
    /**
     * Process constructor.
     *
     * @param string $serviceName
     */
    public function __construct($serviceName)
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
