<?php

namespace Jaeger\Span\Batch;

use Jaeger\Thrift\Batch;
use Jaeger\Process\Process;

class SpanBatch extends Batch
{
    public function __construct(Process $process, array $spans = [])
    {
        $this->process = $process;
        $this->spans = $spans;
        parent::__construct();
    }
}
