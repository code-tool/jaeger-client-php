<?php
declare(strict_types=1);

namespace Jaeger\Tag;

class DbStatementTag extends StringTag
{
    public function __construct(string $value)
    {
        parent::__construct('db.statement', $value);
    }
}
