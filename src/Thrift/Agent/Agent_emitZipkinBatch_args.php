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

class Agent_emitZipkinBatch_args
{
    static public $isValidate = false;

    static public $_TSPEC = array(
        1 => array(
            'var' => 'spans',
            'isRequired' => false,
            'type' => TType::LST,
            'etype' => TType::STRUCT,
            'elem' => array(
                'type' => TType::STRUCT,
                'class' => '\Jaeger\Thrift\Agent\Zipkin\Span',
                ),
        ),
    );

    /**
     * @var \Jaeger\Thrift\Agent\Zipkin\Span[]
     */
    public $spans = null;

    public function __construct($vals = null)
    {
        if (is_array($vals)) {
            if (isset($vals['spans'])) {
                $this->spans = $vals['spans'];
            }
        }
    }

    public function getName()
    {
        return 'Agent_emitZipkinBatch_args';
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
                    if ($ftype == TType::LST) {
                        $this->spans = array();
                        $_size0 = 0;
                        $_etype3 = 0;
                        $xfer += $input->readListBegin($_etype3, $_size0);
                        for ($_i4 = 0; $_i4 < $_size0; ++$_i4) {
                            $elem5 = null;
                            $elem5 = new \Jaeger\Thrift\Agent\Zipkin\Span();
                            $xfer += $elem5->read($input);
                            $this->spans []= $elem5;
                        }
                        $xfer += $input->readListEnd();
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
        $xfer += $output->writeStructBegin('Agent_emitZipkinBatch_args');
        if ($this->spans !== null) {
            if (!is_array($this->spans)) {
                throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
            }
            $xfer += $output->writeFieldBegin('spans', TType::LST, 1);
            $output->writeListBegin(TType::STRUCT, count($this->spans));
            foreach ($this->spans as $iter6) {
                $xfer += $iter6->write($output);
            }
            $output->writeListEnd();
            $xfer += $output->writeFieldEnd();
        }
        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();
        return $xfer;
    }
}
