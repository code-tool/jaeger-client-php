<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Span\Batch;

use CodeTool\OpenTracing\Jaeger\Thrift\Batch;

class SpanBatch extends Batch
{
    public function __construct(array $spans = [])
    {
        $this->spans = $spans;
        parent::__construct();
    }
}
