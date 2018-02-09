<?php
namespace Jaeger\Tracer;

use Jaeger\Span\SpanInterface;

interface DebuggableInterface
{
    /**
     * @param string $requestId
     *
     * @return DebuggableInterface
     */
    public function enable($requestId);

    /**
     * @return DebuggableInterface
     *
     * @return DebuggableInterface
     */
    public function disable();

    /**
     * @param string $operationName
     * @param array  $tags
     *
     * @return SpanInterface
     */
    public function debug($operationName, array $tags = []);
}
