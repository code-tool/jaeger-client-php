<?php
declare(strict_types=1);

namespace Jaeger\Tag;

class DbUser extends StringTag
{
    public function __construct(string $value)
    {
        parent::__construct('db.user', $value);
    }
}
