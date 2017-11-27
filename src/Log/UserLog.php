<?php

namespace Jaeger\Log;

use Jaeger\Tag\StringTag;

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
                new StringTag('event', 'log'),
                new StringTag('level', $level),
                new StringTag('message', $message),
            ]
        );
    }
}
