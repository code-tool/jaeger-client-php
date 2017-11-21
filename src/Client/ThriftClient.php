<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Client;

use CodeTool\OpenTracing\Jaeger\Thrift\Agent\AgentIf as AgentInterface;
use CodeTool\OpenTracing\Process\Process;
use CodeTool\OpenTracing\Span\Batch\SpanBatch;
use CodeTool\OpenTracing\Span\SpanInterface;

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
        $this->agent->emitBatch(new SpanBatch(new Process($this->serviceName), $this->spans));
        $this->spans = [];

        return $this;
    }
}
