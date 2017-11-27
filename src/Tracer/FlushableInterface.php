<?php
declare(strict_types=1);

namespace Jaeger\Tracer;

interface FlushableInterface
{
    public function flush(): FlushableInterface;
}
