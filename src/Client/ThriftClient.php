<?php

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

    /**
     * ThriftClient constructor.
     *
     * @param string         $serviceName
     * @param AgentInterface $agent
     */
    public function __construct($serviceName, AgentInterface $agent)
    {
        $this->serviceName = $serviceName;
        $this->agent = $agent;
    }

    /**
     * @param SpanInterface $span
     *
     * @return ClientInterface
     */
    public function add(SpanInterface $span)
    {
        $this->spans[] = $span;

        return $this;
    }

    /**
     * @return ClientInterface
     */
    public function flush()
    {
        $this->agent->emitBatch(new SpanBatch(new Process($this->serviceName), $this->spans));
        $this->spans = [];

        return $this;
    }
}
