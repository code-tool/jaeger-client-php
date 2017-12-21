<?php
namespace Jaeger\Tag;

class DbUser extends StringTag
{
    /**
     * DbUser constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('db.user', $value);
    }
}
