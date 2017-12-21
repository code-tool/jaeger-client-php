<?php
declare(strict_types=1);

namespace Jaeger\Tag;

abstract class AbstractSpanKindTag extends StringTag
{
    public function __construct(string $value)
    {
        parent::__construct('span.kind', $value);
    }
}
