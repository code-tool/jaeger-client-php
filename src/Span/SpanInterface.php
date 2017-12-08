<?php

namespace Jaeger\Span;

use Jaeger\Thrift\Log;
use Jaeger\Thrift\Tag;
use Jaeger\Span\Context\ContextAwareInterface;

interface SpanInterface extends ContextAwareInterface
{
    /**
     * @param int $finishedAt
     *
     * @return SpanInterface
     */
    public function finish($finishedAt = 0);

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
