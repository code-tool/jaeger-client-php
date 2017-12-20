<?php
declare(strict_types=1);

namespace Jaeger\Tag;

class ErrorTag extends BoolTag
{
    public function __construct()
    {
        parent::__construct('error', true);
    }
}
