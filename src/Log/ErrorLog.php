<?php

namespace Jaeger\Log;

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
                new EventTag('error'),
                new MessageTag($message),
                new StackTag($stack)
            ]
        );
    }
}
