<?php

declare(strict_types=1);

namespace Jaeger\Tests\Fixture;

use Jaeger\Thrift\Agent\AgentIf;
use Jaeger\Thrift\Batch;
use LogicException;

/**
 * A real agent that keeps every batch handed to it instead of putting it on the wire.
 */
final class RecordingAgent implements AgentIf
{
    /**
     * @var list<Batch>
     */
    private array $batches = [];

    public function emitZipkinBatch(?array $spans): void
    {
        throw new LogicException('ThriftClient never emits Zipkin batches');
    }

    public function emitBatch(?Batch $batch): void
    {
        if (!$batch instanceof Batch) {
            throw new LogicException('ThriftClient never emits a null batch');
        }

        $this->batches[] = $batch;
    }

    /**
     * @return list<Batch>
     */
    public function batches(): array
    {
        return $this->batches;
    }
}
