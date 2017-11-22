<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Sampler;

use CodeTool\OpenTracing\Tag\StringTag;

class SamplerParamTag extends StringTag
{
    public function __construct(string $param)
    {
        parent::__construct('sampler.param', $param);
    }
}
