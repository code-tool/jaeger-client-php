<?php

namespace CodeTool\OpenTracing\Process;

use CodeTool\OpenTracing\Tag\LongTag;

class ProcessPidTag extends LongTag
{
    public function __construct()
    {
        parent::__construct('process.pid', getmypid());
    }
}
