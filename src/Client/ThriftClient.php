<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Client;

use CodeTool\OpenTracing\Jaeger\Thrift\Agent\AgentIf as AgentInterface;
use CodeTool\OpenTracing\Span\SpanInterface;

class ThriftClient implements ClientInterface
{
    private $agent;

    private $spans = [];

    public function __construct(AgentInterface $agent)
    {
        $this->agent = $agent;
    }

    public function add(SpanInterface $span): ClientInterface
    {
        $this->spans[] = $span;

        return $this;
    }

    public function flush(): ClientInterface
    {
        //$this->agent->emitBatch(new SpanBatch($this->spans));
        $this->agent->emitZipkinBatch($this->spans);
        $this->spans = [];

        return $this;
    }
}
