<?php

declare(strict_types=1);

namespace Jaeger\Tracer;

use Jaeger\Span\SpanInterface;

/**
 * Interface FinishableInterface
 * @internal use SpanInterface::finish(int $duration = 0)
 */
interface FinishableInterface
{
    public function finish(SpanInterface $span, int $duration = 0): void;
}
