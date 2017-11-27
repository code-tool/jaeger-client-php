<?php

namespace Jaeger\Sampler;

use Jaeger\Tag\StringTag;

class SamplerTypeTag extends StringTag
{
    /**
     * SamplerTypeTag constructor.
     *
     * @param string $type
     */
    public function __construct($type)
    {
        parent::__construct('sampler.type', $type);
    }
}
