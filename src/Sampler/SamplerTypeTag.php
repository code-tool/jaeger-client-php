<?php

namespace CodeTool\OpenTracing\Sampler;

use CodeTool\OpenTracing\Tag\StringTag;

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
