<?php
declare(strict_types=1);

namespace Jaeger\Sampler;

use Jaeger\Tag\LongTag;

class SamplingPriorityTag extends LongTag
{
    public function __construct(int $value)
    {
        parent::__construct('sampling.priority', $value);
    }
}
