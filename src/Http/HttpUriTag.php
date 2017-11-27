<?php
declare(strict_types=1);

namespace Jaeger\Http;

use Jaeger\Tag\StringTag;

class HttpUriTag extends StringTag
{
    public function __construct(string $uri)
    {
        parent::__construct('http.url', $uri);
    }
}
