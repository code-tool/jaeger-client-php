<?php
declare(strict_types=1);

namespace Jaeger\Span\Batch;

use Jaeger\Thrift\Batch;
use Jaeger\Process\CliProcess;

class SpanBatch extends Batch
{
    public function __construct(CliProcess $process, array $spans = [])
    {
        $this->process = $process;
        $this->spans = $spans;
        parent::__construct();
    }
}
