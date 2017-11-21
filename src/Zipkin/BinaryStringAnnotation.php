<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Zipkin;

use CodeTool\OpenTracing\Jaeger\Thrift\Agent\Zipkin\AnnotationType;

class BinaryStringAnnotation extends AbstractBinaryAnnotation
{
    public function __construct(string $key, $value, Endpoint $host)
    {
        parent::__construct($key, $value, AnnotationType::STRING, $host);
    }
}
