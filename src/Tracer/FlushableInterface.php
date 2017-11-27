<?php

namespace Jaeger\Tracer;

interface FlushableInterface
{
    /**
     * @return FlushableInterface
     */
    public function flush();
}
