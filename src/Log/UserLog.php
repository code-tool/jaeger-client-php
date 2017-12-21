<?php
declare(strict_types=1);

namespace Jaeger\Log;

class UserLog extends AbstractLog
{
    public function __construct(string $level, string $message)
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
