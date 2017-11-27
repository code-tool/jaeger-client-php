<?php
declare(strict_types=1);

namespace Jaeger\Tag;

use Jaeger\Thrift\TagType;

class DoubleTag extends AbstractTag
{
    public function __construct(string $key, float $value)
    {
        parent::__construct($key, TagType::DOUBLE, null, $value, null, null, null);
    }
}
