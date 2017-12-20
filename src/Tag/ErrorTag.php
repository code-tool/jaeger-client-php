<?php
namespace Jaeger\Tag;

class ErrorTag extends BoolTag
{
    public function __construct()
    {
        parent::__construct('error', true);
    }
}
