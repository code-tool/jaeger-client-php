<?php

namespace Jaeger\Sampler;

class SamplerResult
{
    private $sampled;

    private $flags;

    private $tags;

    /**
     * SamplerResult constructor.
     *
     * @param bool  $sampled
     * @param int   $flags
     * @param array $tags
     */
    public function __construct($sampled, $flags, array $tags = [])
    {
        $this->sampled = $sampled;
        $this->flags = $flags;
        $this->tags = $tags;
    }

    /**
     * @return int
     */
    public function getFlags()
    {
        return $this->flags;
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
