<?php
namespace Jaeger\Sampler;

interface GeneratorInterface
{
    /**
     * @param int    $traceId
     * @param string $operationName
     *
     * @return string
     */
    public function generate($traceId, $operationName);
}
