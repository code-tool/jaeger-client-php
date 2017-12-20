<?php
namespace Jaeger\Sampler;

use Jaeger\Tag\BoolTag;

class SamplerDecisionTag extends BoolTag
{
    /**
     * SamplerDecisionTag constructor.
     *
     * @param bool $decision
     */
    public function __construct($decision)
    {
        parent::__construct('sampler.decision', $decision);
    }
}
