<?php

namespace Jaeger\Sampler;

use Jaeger\Tag\LongTag;

class SamplingPriorityTag extends LongTag
{
    public function __construct($value)
    {
        parent::__construct('sampling.priority', $value);
    }
}
