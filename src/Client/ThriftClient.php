<?php
declare(strict_types=1);

namespace Jaeger\Client;

use Jaeger\Process\FpmProcess;
use Jaeger\Process\InternalServerProcess;
use Jaeger\Thrift\Agent\AgentIf as AgentInterface;
use Jaeger\Process\CliProcess;
use Jaeger\Span\Batch\SpanBatch;
use Jaeger\Span\SpanInterface;

class ThriftClient implements ClientInterface
{
    private $serviceName;

    private $agent;

    private $spans = [];

    public function __construct(string $serviceName, AgentInterface $agent)
    {
        $this->serviceName = $serviceName;
        $this->agent = $agent;
    }

    public function add(SpanInterface $span): ClientInterface
    {
        $this->spans[] = $span;

        return $this;
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
        $this->agent->emitBatch(new SpanBatch($process, $this->spans));
        $this->spans = [];

        return $this;
    }
}
