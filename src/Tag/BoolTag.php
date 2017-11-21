<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Tag;

use CodeTool\OpenTracing\Jaeger\Thrift\TagType;

class BoolTag extends AbstractTag
{
    public function __construct(string $key, bool $value)
    {
        parent::__construct($key, TagType::BOOL, null, null, $value, null, null);
    }
}
