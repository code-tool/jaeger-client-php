<?php
declare(strict_types=1);

namespace Jaeger\Protocol;

use Thrift\Type\TType;

class TCompactProtocol extends \Thrift\Protocol\TCompactProtocol
{
    public function readFieldBegin(&$name, &$field_type, &$field_id)
    {
        $result = $this->readUByte($compact_type_and_delta);
        $compact_type = $compact_type_and_delta & 0x0f;
        if ($compact_type == TType::STOP) {
            $field_type = $compact_type;
            $field_id = 0;
            return $result;
        }
        $delta = $compact_type_and_delta >> 4;
        if ($delta == 0) {
            $result += $this->readI16($field_id);
        } else {
            $field_id = $this->lastFid + $delta;
        }
        $this->lastFid = $field_id;
        $field_type = $this->getTType($compact_type);
        if ($compact_type == TCompactProtocol::COMPACT_TRUE) {
            $this->state = TCompactProtocol::STATE_BOOL_READ;
            $this->boolValue = true;
        } elseif ($compact_type == TCompactProtocol::COMPACT_FALSE) {
            $this->state = TCompactProtocol::STATE_BOOL_READ;
            $this->boolValue = false;
        } else {
            $this->state = TCompactProtocol::STATE_VALUE_READ;
        }
        return $result;
    }
}
