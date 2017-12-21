<?php
namespace Jaeger\Process;

class FpmProcess extends AbstractProcess
{
    /**
     * FpmProcess constructor.
     *
     * @param string $serviceName
     */
    public function __construct($serviceName)
    {
        parent::__construct($serviceName, [new ProcessIpTag()]);
    }
}
