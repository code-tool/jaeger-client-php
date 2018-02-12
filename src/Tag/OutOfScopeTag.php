<?php
namespace Jaeger\Tag;

class OutOfScopeTag extends BoolTag
{
    public function __construct()
    {
        parent::__construct('scope.missing', true);
    }
}
