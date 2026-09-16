<?php

declare(strict_types=1);

namespace Jaeger\Client;

use Jaeger\Process\CliProcess;
use Jaeger\Process\FpmProcess;
use Jaeger\Process\InternalServerProcess;
use Jaeger\Span\Batch\SpanBatch;
use Jaeger\Span\SpanInterface;
use Jaeger\Thrift\Agent\AgentIf as AgentInterface;

class ThriftClient implements ClientInterface
{
    public const int MAX_BATCH_SIZE = 32;

    /**
     * @var list<SpanInterface>
     */
    private array $spans = [];

    public function __construct(
        private readonly string $serviceName,
        private readonly AgentInterface $agent,
        private readonly int $batch = self::MAX_BATCH_SIZE,
    ) {}

    public function add(SpanInterface $span): ClientInterface
    {
        $this->spans[] = $span;

        return $this;
    }

    public function getSpans(): array
    {
        return $this->spans;
    }

    public function flush(): ClientInterface
    {
        $process = match (PHP_SAPI) {
            'cli' => new CliProcess($this->serviceName),
            'cli-server' => new InternalServerProcess($this->serviceName),
            default => new FpmProcess($this->serviceName),
        };
        foreach (array_chunk($this->spans, $this->batch) as $batch) {
            $this->agent->emitBatch(new SpanBatch($process, $batch));
        }

        $this->spans = [];

        return $this;
    }
}
