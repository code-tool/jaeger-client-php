<?php

namespace Jaeger\Span\Batch;

use Jaeger\Process\AbstractProcess;
use Jaeger\Thrift\Batch;

class SpanBatch extends Batch
{
    public function __construct(AbstractProcess $process, array $spans = [])
    {
        $this->process = $process;
        $this->spans = $spans;
        parent::__construct();
    }
}
