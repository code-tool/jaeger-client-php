<?php

namespace CodeTool\OpenTracing\Sampler;

class ConstSampler implements SamplerInterface
{
    const SAMPLER_TYPE_CONST = '';

    private $debugEnabled;

    /**
     * ConstSampler constructor.
     *
     * @param bool $debugEnabled
     */
    public function __construct($debugEnabled)
    {
        $this->debugEnabled = $debugEnabled;
    }

    /**
     * @param int    $traceId
     * @param string $operationName
     *
     * @return SamplerResult
     */
    public function decide($traceId, $operationName)
    {
        return new SamplerResult(
            $this->debugEnabled,
            [
                new SamplerTypeTag('const'),
                new SamplerParamTag($this->debugEnabled ? 'True' : 'False')
            ]
        );
    }
}
