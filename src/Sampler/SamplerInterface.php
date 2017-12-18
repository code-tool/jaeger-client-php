<?php

namespace Jaeger\Sampler;

interface SamplerInterface
{
    /**
     * @param int    $traceId
     * @param string $operationName
     * @param bool   $isDebug
     *
     * @return SamplerResult
     */
    public function decide($traceId, $operationName, $isDebug);
}
