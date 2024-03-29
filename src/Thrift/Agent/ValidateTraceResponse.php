<?php
namespace Jaeger\Thrift\Agent;

/**
 * Autogenerated by Thrift Compiler (0.15.0)
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 *  @generated
 */
use Thrift\Base\TBase;
use Thrift\Type\TType;
use Thrift\Type\TMessageType;
use Thrift\Exception\TException;
use Thrift\Exception\TProtocolException;
use Thrift\Protocol\TProtocol;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Exception\TApplicationException;

class ValidateTraceResponse
{
    static public $isValidate = false;

    static public $_TSPEC = array(
        1 => array(
            'var' => 'ok',
            'isRequired' => true,
            'type' => TType::BOOL,
        ),
        2 => array(
            'var' => 'traceCount',
            'isRequired' => true,
            'type' => TType::I64,
        ),
    );

    /**
     * @var bool
     */
    public $ok = null;
    /**
     * @var int
     */
    public $traceCount = null;

    public function __construct($vals = null)
    {
        if (is_array($vals)) {
            if (isset($vals['ok'])) {
                $this->ok = $vals['ok'];
            }
            if (isset($vals['traceCount'])) {
                $this->traceCount = $vals['traceCount'];
            }
        }
    }

    public function getName()
    {
        return 'ValidateTraceResponse';
    }


    public function read($input)
    {
        $xfer = 0;
        $fname = null;
        $ftype = 0;
        $fid = 0;
        $xfer += $input->readStructBegin($fname);
        while (true) {
            $xfer += $input->readFieldBegin($fname, $ftype, $fid);
            if ($ftype == TType::STOP) {
                break;
            }
            switch ($fid) {
                case 1:
                    if ($ftype == TType::BOOL) {
                        $xfer += $input->readBool($this->ok);
                    } else {
                        $xfer += $input->skip($ftype);
                    }
                    break;
                case 2:
                    if ($ftype == TType::I64) {
                        $xfer += $input->readI64($this->traceCount);
                    } else {
                        $xfer += $input->skip($ftype);
                    }
                    break;
                default:
                    $xfer += $input->skip($ftype);
                    break;
            }
            $xfer += $input->readFieldEnd();
        }
        $xfer += $input->readStructEnd();
        return $xfer;
    }

    public function write($output)
    {
        $xfer = 0;
        $xfer += $output->writeStructBegin('ValidateTraceResponse');
        if ($this->ok !== null) {
            $xfer += $output->writeFieldBegin('ok', TType::BOOL, 1);
            $xfer += $output->writeBool($this->ok);
            $xfer += $output->writeFieldEnd();
        }
        if ($this->traceCount !== null) {
            $xfer += $output->writeFieldBegin('traceCount', TType::I64, 2);
            $xfer += $output->writeI64($this->traceCount);
            $xfer += $output->writeFieldEnd();
        }
        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();
        return $xfer;
    }
}
