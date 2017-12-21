<?php
declare(strict_types=1);

namespace Jaeger\Log;

class ErrorLog extends AbstractLog
{
    public function __construct(string $message, string $stack)
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
