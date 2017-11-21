<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Tag;

use CodeTool\OpenTracing\Jaeger\Thrift\TagType;

class StringTag extends AbstractTag
{
    public function __construct(string $key, string $value)
    {
        parent::__construct($key, TagType::STRING, $value, null, null, null, null);
    }
}
