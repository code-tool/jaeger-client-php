<?php

namespace Jaeger\Client;

use Jaeger\Process\FpmProcess;
use Jaeger\Thrift\Agent\AgentIf as AgentInterface;
use Jaeger\Process\CliProcess;
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
        if (PHP_SAPI === 'cli') {
            $process = new CliProcess($this->serviceName);
        } else {
            $process = new FpmProcess($this->serviceName);
        }
        $this->agent->emitBatch(new SpanBatch($process, $this->spans));
        $this->spans = [];

        return $this;
    }
}
