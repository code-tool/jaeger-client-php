<?php

namespace Jaeger\Sampler;

use Jaeger\Tag\StringTag;

class SamplerParamTag extends StringTag
{
    /**
     * SamplerParamTag constructor.
     *
     * @param string $param
     */
    public function __construct($param)
    {
        parent::__construct('sampler.param', $param);
    }
}
