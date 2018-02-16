<?php

namespace Jaeger\Log;

class UserLog extends AbstractLog
{
    /**
     * UserLog constructor.
     *
     * @param string $name
     * @param string $level
     * @param string $message
     * @param int    $timestamp
     */
    public function __construct($name, $level, $message, $timestamp = 0)
    {
        parent::__construct(
            [
                new EventTag($name),
                new LevelTag($level),
                new MessageTag($message),
            ],
            $timestamp
        );
    }
}
