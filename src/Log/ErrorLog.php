<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Log;

use CodeTool\OpenTracing\Tag\StringTag;

class ErrorLog extends AbstractLog
{
    public function __construct(string $message, string $stack)
    {
        parent::__construct(
            [
                new StringTag('event', 'error'),
                new StringTag('message', $message),
                new StringTag('stack', $stack)
            ]
        );
    }
}
