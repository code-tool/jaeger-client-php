<?php
declare(strict_types=1);

namespace CodeTool\OpenTracing\Zipkin;

abstract class AbstractBinaryAnnotation extends \CodeTool\OpenTracing\Jaeger\Thrift\Agent\Zipkin\BinaryAnnotation
{
    public function __construct(string $key, $value, int $type, string $host)
    {
        $this->key = $key;
        $this->value = $value;
        $this->annotation_type = $type;
        $this->host = $host;
        parent::__construct();
    }
}
