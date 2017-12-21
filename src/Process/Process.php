<?php
declare(strict_types=1);

namespace Jaeger\Process;

use Jaeger\General\JaegerHostnameTag;
use Jaeger\General\JaegerVersionTag;
use Jaeger\General\PhpBinaryTag;
use Jaeger\General\PhpVersionTag;

class Process extends \Jaeger\Thrift\Process
{
    public function __construct(string $serviceName)
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
