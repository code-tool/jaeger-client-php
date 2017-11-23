<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Tracer;

interface FlushableInterface
{
    public function flush(): FlushableInterface;
}
