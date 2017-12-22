<?php

namespace Jaeger\Http;

use Jaeger\Tag\StringTag;

class HttpUriTag extends StringTag
{
    /**
     * HttpUriTag constructor.
     *
     * @param string $uri
     */
    public function __construct($uri)
    {
        parent::__construct('http.url', $uri);
    }
}
