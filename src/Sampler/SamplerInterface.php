<?php

namespace Jaeger\Sampler;

interface SamplerInterface
{
    /**
     * @param int    $traceId
     * @param string $operationName
     * @param bool   $debugId
     *
     * @return SamplerResult
     */
    public function decide($traceId, $operationName, $debugId);
}
