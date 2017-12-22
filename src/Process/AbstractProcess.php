<?php

namespace Jaeger\Process;

use Jaeger\General\JaegerHostnameTag;
use Jaeger\General\JaegerVersionTag;
use Jaeger\General\PhpBinaryTag;
use Jaeger\General\PhpVersionTag;
use Jaeger\Thrift\Process;

abstract class AbstractProcess extends Process
{
    /**
     * AbstractProcess constructor.
     *
     * @param string $serviceName
     * @param array  $tags
     */
    public function __construct($serviceName, array $tags = [])
    {
        $this->serviceName = $serviceName;
        $this->tags = array_merge(
            $tags,
            [
                new JaegerVersionTag(),
                new JaegerHostnameTag(),
                new PhpBinaryTag(),
                new PhpVersionTag(),
                new ProcessPidTag(),
                new ProcessSapiTag(),
                new ProcessUidTag(),
                new ProcessGidTag()
            ]
        );
        parent::__construct();
    }
}
