<?php

namespace Jaeger\Tag;

class PeerHostnameTag extends StringTag
{
    /**
     * PeerHostnameTag constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('peer.hostname', $value);
    }
}
