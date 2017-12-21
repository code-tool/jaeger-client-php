<?php
declare(strict_types=1);

namespace Jaeger\Process;

class FpmProcess extends AbstractProcess
{
    public function __construct(string $serviceName)
    {
        parent::__construct($serviceName, [new ProcessIpTag()]);
    }
}
