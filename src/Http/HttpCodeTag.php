<?php
declare(strict_types=1);

namespace Jaeger\Http;

use Jaeger\Tag\LongTag;

class HttpCodeTag extends LongTag
{
    public function __construct(int $code)
    {
        parent::__construct('http.status_code', $code);
    }
}
