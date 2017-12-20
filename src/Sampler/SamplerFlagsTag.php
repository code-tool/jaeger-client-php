<?php
declare(strict_types=1);

namespace Jaeger\Sampler;

use Jaeger\Tag\LongTag;

class SamplerFlagsTag extends LongTag
{
    public function __construct(int $flags)
    {
        parent::__construct('sampler.flags', $flags);
    }
}
