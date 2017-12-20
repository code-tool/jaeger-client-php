<?php
declare(strict_types=1);

namespace Jaeger\Sampler;

use Jaeger\Tag\BoolTag;

class SamplerDecisionTag extends BoolTag
{
    public function __construct(bool $decision)
    {
        parent::__construct('sampler.decision', $decision);
    }
}
