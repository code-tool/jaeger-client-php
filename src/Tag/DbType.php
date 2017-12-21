<?php
declare(strict_types=1);

namespace Jaeger\Tag;

class DbType extends StringTag
{
    public function __construct(string $value)
    {
        parent::__construct('db.type', $value);
    }
}
