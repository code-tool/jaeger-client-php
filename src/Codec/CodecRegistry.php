<?php

namespace CodeTool\OpenTracing\Codec;

class CodecRegistry implements \ArrayAccess
{
    private $codecs = [];

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->codecs);
    }

    public function offsetGet($offset)
    {
        if (false === array_key_exists($offset, $this->codecs)) {
            return null;
        }

        return $this->codecs[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->codecs[$offset] = $value;

        return $this;
    }

    public function offsetUnset($offset)
    {
        if (false === array_key_exists($offset, $this->codecs)) {
            return $this;
        }
        unset($this->codecs[$offset]);

        return $this;
    }

}
