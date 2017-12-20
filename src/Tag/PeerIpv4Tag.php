<?php
declare(strict_types=1);

namespace Jaeger\Tag;

class PeerIpv4Tag extends StringTag
{
    public function __construct(string $value)
    {
        parent::__construct('peer.ip', $value);
    }
}
