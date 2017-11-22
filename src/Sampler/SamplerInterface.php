<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Sampler;

interface SamplerInterface
{
    const SAMPLER_TYPE_TAG_KEY = 'sampler.type';
    const SAMPLER_PARAM_TAG_KEY = 'sampler.param';

    public function decide(int $traceId, string $operationName) : SamplerResult;
}
