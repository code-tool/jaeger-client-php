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
    /**
     * @param SpanInterface $span
     * @param int           $duration
     *
     * @return mixed
     */
    public function finish(SpanInterface $span, int $duration = 0): void;
}
