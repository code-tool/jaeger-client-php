<?php
declare(strict_types=1);

namespace Jaeger\Client;

use Jaeger\Span\SpanInterface;

interface ClientInterface
{
    public function add(SpanInterface $span) : ClientInterface;

    public function flush() : ClientInterface;
}
