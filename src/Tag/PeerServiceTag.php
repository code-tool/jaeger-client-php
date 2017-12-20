<?php
declare(strict_types=1);

namespace Jaeger\Tag;

class PeerServiceTag extends StringTag
{
    public function __construct(string $value)
    {
        parent::__construct('peer.service', $value);
    }
}
