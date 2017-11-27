<?php
declare(strict_types=1);

namespace Jaeger\Http;

use Jaeger\Tag\StringTag;

class HttpMethodTag extends StringTag
{
    public function __construct(string $method)
    {
        parent::__construct('http.method', $method);
    }
}
