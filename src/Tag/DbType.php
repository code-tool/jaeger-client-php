<?php
namespace Jaeger\Tag;

class DbType extends StringTag
{
    /**
     * DbType constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('db.type', $value);
    }
}
