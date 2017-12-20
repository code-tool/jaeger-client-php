<?php
namespace Jaeger\Tag;

class PeerPortTag extends LongTag
{
    /**
     * PeerPortTag constructor.
     *
     * @param int $value
     */
    public function __construct($value)
    {
        parent::__construct('peer.port', (int)$value);
    }
}
