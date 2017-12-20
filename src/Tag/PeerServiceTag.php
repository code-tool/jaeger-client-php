<?php
namespace Jaeger\Tag;

class PeerServiceTag extends StringTag
{
    /**
     * PeerServiceTag constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('peer.service', $value);
    }
}
