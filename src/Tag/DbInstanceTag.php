<?php
declare(strict_types=1);

namespace Jaeger\Tag;

class DbInstanceTag extends StringTag
{
    public function __construct(string $value)
    {
        parent::__construct('db.instance', $value);
    }
}
