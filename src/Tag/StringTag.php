<?php
declare(strict_types=1);

namespace Jaeger\Tag;

use Jaeger\Thrift\TagType;

class StringTag extends AbstractTag
{
    public function __construct(string $key, string $value)
    {
        parent::__construct($key, TagType::STRING, $value, null, null, null, null);
    }
}
