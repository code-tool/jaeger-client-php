<?php
namespace Jaeger\Tag;

class DbStatementTag extends StringTag
{
    /**
     * DbStatementTag constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('db.statement', $value);
    }
}
