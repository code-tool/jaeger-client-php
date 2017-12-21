<?php
declare(strict_types=1);

namespace Jaeger\Tag;

class MessageBusDestinationTag extends StringTag
{
    public function __construct(string $value)
    {
        parent::__construct('message_bus.destination', $value);
    }
}
