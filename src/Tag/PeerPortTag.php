<?php
declare(strict_types=1);

namespace Jaeger\Tag;

class PeerPortTag extends LongTag
{
    public function __construct(int $value)
    {
        parent::__construct('peer.port', $value);
    }
}
