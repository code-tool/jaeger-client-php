<?php

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

    public function getSpans() : array
    {
        return $this->spans;
    }

    /**
     * @return ClientInterface
     */
    public function flush()
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
