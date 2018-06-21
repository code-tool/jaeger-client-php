<?php
namespace Jaeger\Sampler;

class OperationGenerator implements GeneratorInterface
{
    /**
     * @param int    $traceId
     * @param string $operationName
     *
     * @return string
     */
    public function generate($traceId, $operationName)
    {
        return 'operation:' . $operationName;
    }
}
