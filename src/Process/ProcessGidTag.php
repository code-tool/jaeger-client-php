<?php

namespace Jaeger\Process;

use Jaeger\Tag\LongTag;

class ProcessGidTag extends LongTag
{
    public function __construct()
    {
        parent::__construct('process.gid', getmygid());
    }
}
