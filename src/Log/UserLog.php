<?php

namespace Jaeger\Log;

class UserLog extends AbstractLog
{
    /**
     * UserLog constructor.
     *
     * @param string $level
     * @param string $message
     * @param int    $timestamp
     */
    public function __construct($level, $message, $timestamp = 0)
    {
        parent::__construct(
            [
                new EventTag('log'),
                new LevelTag($level),
                new MessageTag($message),
            ],
            $timestamp
        );
    }
}
