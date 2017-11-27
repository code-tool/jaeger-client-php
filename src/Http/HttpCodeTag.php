<?php
namespace Jaeger\Http;

use Jaeger\Tag\LongTag;

class HttpCodeTag extends LongTag
{
    /**
     * HttpCodeTag constructor.
     *
     * @param int $code
     */
    public function __construct($code)
    {
        parent::__construct('http.code', $code);
    }
}
