<?php
namespace Jaeger\Http;

use Jaeger\Tag\StringTag;

class HttpMethodTag extends StringTag
{
    /**
     * HttpMethodTag constructor.
     *
     * @param string $method
     */
    public function __construct($method)
    {
        parent::__construct('http.method', $method);
    }
}
