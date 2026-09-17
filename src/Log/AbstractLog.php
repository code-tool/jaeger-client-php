<?php

declare(strict_types=1);

namespace Jaeger\Log;

use Jaeger\Thrift\Log;
use Jaeger\Thrift\Tag;

abstract class AbstractLog extends Log
{
    /**
     * @param list<Tag> $tags
     */
    public function __construct(
        array $tags = [],
        int $timestamp = 0,
    ) {
        $this->timestamp = 0 !== $timestamp ? $timestamp : (int) round(microtime(true) * 1000000.0);
        $this->fields = $tags;
        parent::__construct();
    }
}
