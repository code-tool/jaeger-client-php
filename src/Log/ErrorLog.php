<?php

namespace Jaeger\Log;

class ErrorLog extends AbstractLog
{
    /**
     * ErrorLog constructor.
     *
     * @param string $message
     * @param string $stack
     * @param int    $timestamp
     */
    public function __construct($message, $stack, $timestamp = 0)
    {
        parent::__construct(
            [
                new EventTag('error'),
                new MessageTag($message),
                new StackTag($stack)
            ],
            $timestamp
        );
    }
}
