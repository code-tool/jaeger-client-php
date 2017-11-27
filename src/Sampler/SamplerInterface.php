<?php

namespace CodeTool\OpenTracing\Sampler;

interface SamplerInterface
{
    const SAMPLER_TYPE_TAG_KEY = 'sampler.type';
    const SAMPLER_PARAM_TAG_KEY = 'sampler.param';

    /**
     * @param int    $traceId
     * @param string $operationName
     *
     * @return SamplerResult
     */
    public function decide($traceId, $operationName);
}
