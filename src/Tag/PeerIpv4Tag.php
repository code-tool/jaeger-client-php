<?php
namespace Jaeger\Tag;

class PeerIpv4Tag extends StringTag
{
    /**
     * PeerIpv4Tag constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('peer.ip', $value);
    }
}
