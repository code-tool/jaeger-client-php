<?php

namespace CodeTool\OpenTracing\Span;

use CodeTool\OpenTracing\Jaeger\Thrift\Log;
use CodeTool\OpenTracing\Jaeger\Thrift\Tag;
use CodeTool\OpenTracing\Span\Context\ContextAwareInterface;

interface SpanInterface extends ContextAwareInterface
{
    /**
     * @return SpanInterface
     */
    public function finish();

    /**,
     * @param Tag $tag
     *
     * @return SpanInterface
     */
    public function addTag(Tag $tag);

    /**
     * @param Log $log
     *
     * @return SpanInterface
     */
    public function addLog(Log $log);

    /**
     * @param string $key
     * @param mixed  $item
     *
     * @return SpanInterface
     */
    public function withItem($key, $item);

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getItem($key, $default = null);

    /**
     * @param string $key
     *
     * @return SpanInterface
     */
    public function withoutItem($key);
}
