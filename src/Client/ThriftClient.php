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
    public const MAX_BATCH_SIZE = 32;

    private $serviceName;

    private $agent;

    private $batch;

    private $spans = [];

    public function __construct(string $serviceName, AgentInterface $agent, $batch = self::MAX_BATCH_SIZE)
    {
        $this->serviceName = $serviceName;
        $this->agent = $agent;
        $this->batch = (int)$batch;
    }

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
        switch (PHP_SAPI) {
            case 'cli':
                $process = new CliProcess($this->serviceName);
                break;
            case 'cli-server':
                $process = new InternalServerProcess($this->serviceName);
                break;
            default:
                $process = new FpmProcess($this->serviceName);
                break;
        }
        foreach (array_chunk($this->spans, $this->batch) as $batch) {
            $this->agent->emitBatch(new SpanBatch($process, $batch));
        }
        $this->spans = [];

        return $this;
    }
}
