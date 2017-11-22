<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Process;

use CodeTool\OpenTracing\Tag\StringTag;

class ProcessSapiTag extends StringTag
{
    public function __construct()
    {
        parent::__construct('process.sapi', php_sapi_name());
    }
}
