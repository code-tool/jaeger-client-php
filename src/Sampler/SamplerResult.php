<?php

namespace CodeTool\OpenTracing\Sampler;

class SamplerResult
{
    private $sampled;

    private $tags;

    /**
     * SamplerResult constructor.
     *
     * @param bool  $sampled
     * @param array $tags
     */
    public function __construct($sampled, array $tags = [])
    {
        $this->sampled = $sampled;
        $this->tags = $tags;
    }

    /**
     * @return bool
     */
    public function isSampled()
    {
        return $this->sampled;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }
}
