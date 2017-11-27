<?php
declare(strict_types=1);

namespace Jaeger\Client;

use Jaeger\Thrift\Agent\AgentIf as AgentInterface;
use Jaeger\Process\Process;
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
        $this->agent->emitBatch(new SpanBatch(new Process($this->serviceName), $this->spans));
        $this->spans = [];

        return $this;
    }
}
