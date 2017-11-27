<?php
declare(strict_types=1);

namespace Jaeger\Tag;

use Jaeger\Thrift\TagType;

class BoolTag extends AbstractTag
{
    public function __construct(string $key, bool $value)
    {
        parent::__construct($key, TagType::BOOL, null, null, $value, null, null);
    }
}
