<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Sampler;

use CodeTool\OpenTracing\Tag\StringTag;

class SamplerTypeTag extends StringTag
{
    public function __construct(string $type)
    {
        parent::__construct('sampler.type', $type);
    }
}
