<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Span\Batch;

use CodeTool\OpenTracing\Jaeger\Thrift\Batch;
use CodeTool\OpenTracing\Process\Process;

class SpanBatch extends Batch
{
    public function __construct(Process $process, array $spans = [])
    {
        $this->process = $process;
        $this->spans = $spans;
        parent::__construct();
    }
}
