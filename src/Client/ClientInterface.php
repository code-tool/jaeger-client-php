<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Client;

use CodeTool\OpenTracing\Span\SpanInterface;

interface ClientInterface
{
    public function add(SpanInterface $span) : ClientInterface;

    public function flush() : ClientInterface;
}
