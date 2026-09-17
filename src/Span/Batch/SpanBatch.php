<?php

declare(strict_types=1);

namespace Jaeger\Span\Batch;

use Jaeger\Process\AbstractProcess;
use Jaeger\Span\SpanInterface;
use Jaeger\Thrift\Batch;

class SpanBatch extends Batch
{
    /**
     * @param array<array-key, SpanInterface> $spans
     *
     * SpanInterface cannot declare that it extends the generated \Jaeger\Thrift\Span, even though
     * its only implementation does, so this assignment has to be taken on trust.
     *
     * @psalm-suppress InvalidPropertyAssignmentValue
     */
    public function __construct(
        AbstractProcess $process,
        array $spans = [],
    ) {
        $this->process = $process;
        $this->spans = $spans;
        parent::__construct();
    }
}
