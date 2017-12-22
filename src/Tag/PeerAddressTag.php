<?php

namespace Jaeger\Tag;

class PeerAddressTag extends StringTag
{
    /**
     * PeerAddressTag constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('peer.adress', $value);
    }
}
