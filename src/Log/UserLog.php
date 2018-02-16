<?php
declare(strict_types=1);

namespace Jaeger\Log;

class UserLog extends AbstractLog
{
    public function __construct(string $name, string $level, string $message, int $timestamp = 0)
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
