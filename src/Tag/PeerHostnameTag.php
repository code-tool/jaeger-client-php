<?php
declare(strict_types=1);

namespace Jaeger\Tag;

class PeerHostnameTag extends StringTag
{
    public function __construct(string $value)
    {
        parent::__construct('peer.hostname', $value);
    }
}
