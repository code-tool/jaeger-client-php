<?php
declare(strict_types=1);

namespace Jaeger\Tag;

class ComponentTag extends StringTag
{
    public function __construct(string $value)
    {
        parent::__construct('component', $value);
    }
}
