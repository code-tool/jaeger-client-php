<?php

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
