<?php

namespace Jaeger\Log;

use Jaeger\Tag\StringTag;

class ErrorLog extends AbstractLog
{
    /**
     * ErrorLog constructor.
     *
     * @param string $message
     * @param string $stack
     */
    public function __construct($message, $stack)
    {
        parent::__construct(
            [
                new StringTag('event', 'error'),
                new StringTag('message', $message),
                new StringTag('stack', $stack)
            ]
        );
    }
}
