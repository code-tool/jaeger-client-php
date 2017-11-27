<?php

namespace CodeTool\OpenTracing\Tracer;

interface FlushableInterface
{
    /**
     * @return FlushableInterface
     */
    public function flush();
}
