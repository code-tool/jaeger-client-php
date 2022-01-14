<?php
declare(strict_types=1);

namespace Jaeger\Span;

use Jaeger\Span\Context\ContextAwareInterface;
use Jaeger\Tracer\InjectableInterface;
use Jaeger\Tracer\ResettableInterface;

interface SpanManagerInterface extends ContextAwareInterface,
                                       InjectableInterface,
                                       ResettableInterface,
                                       SpanAwareInterface
{
    public function new(SpanInterface $span);

    public function finish(SpanInterface $span): ?SpanInterface;
}
