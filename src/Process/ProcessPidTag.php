<?php

namespace Jaeger\Process;

use Jaeger\Tag\LongTag;

class ProcessPidTag extends LongTag
{
    public function __construct()
    {
        parent::__construct('process.pid', getmypid());
    }
}
