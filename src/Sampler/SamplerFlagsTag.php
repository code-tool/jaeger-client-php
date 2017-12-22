<?php

namespace Jaeger\Sampler;

use Jaeger\Tag\LongTag;

class SamplerFlagsTag extends LongTag
{
    /**
     * SamplerFlagsTag constructor.
     *
     * @param int $flags
     */
    public function __construct($flags)
    {
        parent::__construct('sampler.flags', $flags);
    }
}
