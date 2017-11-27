<?php
declare(strict_types=1);

namespace Jaeger\Tag;

use Jaeger\Thrift\TagType;

class LongTag extends AbstractTag
{
    public function __construct(string $key, int $value)
    {
        parent::__construct($key, TagType::LONG, null, null, null, $value, null);
    }
}
