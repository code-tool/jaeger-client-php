<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Zipkin;

use CodeTool\OpenTracing\Jaeger\Thrift\Agent\Zipkin\AnnotationType;

class BinaryBoolAnnotation extends AbstractBinaryAnnotation
{
    public function __construct(string $key, $value, string $host)
    {
        parent::__construct($key, $value, AnnotationType::BOOL, $host);
    }
}
