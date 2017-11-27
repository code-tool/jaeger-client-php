<?php
declare(strict_types=1);

namespace Jaeger\Sampler;

use Jaeger\Tag\StringTag;

class SamplerParamTag extends StringTag
{
    public function __construct(string $param)
    {
        parent::__construct('sampler.param', $param);
    }
}
