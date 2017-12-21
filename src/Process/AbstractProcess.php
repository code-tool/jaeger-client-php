<?php
declare(strict_types=1);

namespace Jaeger\Process;

use Jaeger\General\JaegerHostnameTag;
use Jaeger\General\JaegerVersionTag;
use Jaeger\General\PhpBinaryTag;
use Jaeger\General\PhpVersionTag;
use Jaeger\Thrift\Process;

abstract class AbstractProcess extends Process
{
    public function __construct(string $serviceName, array $tags = [])
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
