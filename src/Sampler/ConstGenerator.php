<?php
namespace Jaeger\Sampler;

class ConstGenerator implements GeneratorInterface
{
    /**
     * @param int    $traceId
     * @param string $operationName
     *
     * @return string
     */
    public function generate($traceId, $operationName)
    {
        return 'const';
    }
}
