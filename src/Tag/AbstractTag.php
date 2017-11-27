<?php

namespace CodeTool\OpenTracing\Tag;

use CodeTool\OpenTracing\Jaeger\Thrift\Tag;

abstract class AbstractTag extends Tag implements TagInterface
{
    /**
     * AbstractTag constructor.
     *
     * @param string      $key
     * @param int         $type
     * @param string|null $vStr
     * @param float|null  $vDouble
     * @param bool|null   $vBool
     * @param int|null    $vLong
     * @param string|null $vBinary
     */
    public function __construct(
        $key,
        $type,
        $vStr = null,
        $vDouble = null,
        $vBool = null,
        $vLong = null,
        $vBinary = null
    ) {
        $this->key = $key;
        $this->vType = $type;
        $this->vStr = $vStr;
        $this->vDouble = $vDouble;
        $this->vBool = $vBool;
        $this->vLong = $vLong;
        $this->vBinary = $vBinary;
        parent::__construct();
    }
}
