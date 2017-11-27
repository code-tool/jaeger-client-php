<?php

namespace CodeTool\OpenTracing\Client;

use CodeTool\OpenTracing\Span\SpanInterface;

interface ClientInterface
{
    /**
     * @param SpanInterface $span
     *
     * @return ClientInterface
     */
    public function add(SpanInterface $span);

    /**
     * @return ClientInterface
     */
    public function flush();
}
