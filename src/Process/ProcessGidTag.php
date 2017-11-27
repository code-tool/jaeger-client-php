<?php

namespace CodeTool\OpenTracing\Process;

use CodeTool\OpenTracing\Tag\LongTag;

class ProcessGidTag extends LongTag
{
    public function __construct()
    {
        parent::__construct('process.gid', getmygid());
    }
}
