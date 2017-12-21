<?php
namespace Jaeger\Tag;

class MessageBusDestinationTag extends StringTag
{
    /**
     * MessageBusDestinationTag constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('message_bus.destination', $value);
    }
}
