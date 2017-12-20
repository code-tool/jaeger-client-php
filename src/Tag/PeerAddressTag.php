<?php
declare(strict_types=1);

namespace Jaeger\Tag;

class PeerAddressTag extends StringTag
{
    public function __construct(string $value)
    {
        parent::__construct('peer.adress', $value);
    }
}
