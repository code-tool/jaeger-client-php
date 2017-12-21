<?php

namespace Jaeger\Log;

class UserLog extends AbstractLog
{
    /**
     * UserLog constructor.
     *
     * @param string $level
     * @param string $message
     */
    public function __construct($level, $message)
    {
        parent::__construct(
            [
                new EventTag('log'),
                new LevelTag($level),
                new MessageTag($message),
            ]
        );
    }
}
