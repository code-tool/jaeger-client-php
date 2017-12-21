<?php

namespace Jaeger\Process;

use Jaeger\General\JaegerHostnameTag;
use Jaeger\General\JaegerVersionTag;
use Jaeger\General\PhpBinaryTag;
use Jaeger\General\PhpVersionTag;

class Process extends \Jaeger\Thrift\Process
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
            new ProcessIpTag(),
            new ProcessPidTag(),
            new ProcessSapiTag(),
            new ProcessUidTag(),
            new ProcessGidTag()
        ];
        parent::__construct();
    }
}
