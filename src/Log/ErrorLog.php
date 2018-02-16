<?php
declare(strict_types=1);

namespace Jaeger\Log;

class ErrorLog extends AbstractLog
{
    public function __construct(string $message, string $stack, int $timestamp = 0)
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
