<?php
namespace CodeTool\OpenTracing;

/**
 * Autogenerated by Thrift Compiler (0.10.0)
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


class SamplingStrategyResponse {
  static $_TSPEC;

  /**
   * @var int
   */
  public $strategyType = null;
  /**
   * @var \CodeTool\OpenTracing\ProbabilisticSamplingStrategy
   */
  public $probabilisticSampling = null;
  /**
   * @var \CodeTool\OpenTracing\RateLimitingSamplingStrategy
   */
  public $rateLimitingSampling = null;
  /**
   * @var \CodeTool\OpenTracing\PerOperationSamplingStrategies
   */
  public $operationSampling = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'strategyType',
          'type' => TType::I32,
          ),
        2 => array(
          'var' => 'probabilisticSampling',
          'type' => TType::STRUCT,
          'class' => '\CodeTool\OpenTracing\ProbabilisticSamplingStrategy',
          ),
        3 => array(
          'var' => 'rateLimitingSampling',
          'type' => TType::STRUCT,
          'class' => '\CodeTool\OpenTracing\RateLimitingSamplingStrategy',
          ),
        4 => array(
          'var' => 'operationSampling',
          'type' => TType::STRUCT,
          'class' => '\CodeTool\OpenTracing\PerOperationSamplingStrategies',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['strategyType'])) {
        $this->strategyType = $vals['strategyType'];
      }
      if (isset($vals['probabilisticSampling'])) {
        $this->probabilisticSampling = $vals['probabilisticSampling'];
      }
      if (isset($vals['rateLimitingSampling'])) {
        $this->rateLimitingSampling = $vals['rateLimitingSampling'];
      }
      if (isset($vals['operationSampling'])) {
        $this->operationSampling = $vals['operationSampling'];
      }
    }
  }

  public function getName() {
    return 'SamplingStrategyResponse';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->strategyType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRUCT) {
            $this->probabilisticSampling = new \CodeTool\OpenTracing\ProbabilisticSamplingStrategy();
            $xfer += $this->probabilisticSampling->read($input);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRUCT) {
            $this->rateLimitingSampling = new \CodeTool\OpenTracing\RateLimitingSamplingStrategy();
            $xfer += $this->rateLimitingSampling->read($input);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRUCT) {
            $this->operationSampling = new \CodeTool\OpenTracing\PerOperationSamplingStrategies();
            $xfer += $this->operationSampling->read($input);
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

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('SamplingStrategyResponse');
    if ($this->strategyType !== null) {
      $xfer += $output->writeFieldBegin('strategyType', TType::I32, 1);
      $xfer += $output->writeI32($this->strategyType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->probabilisticSampling !== null) {
      if (!is_object($this->probabilisticSampling)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('probabilisticSampling', TType::STRUCT, 2);
      $xfer += $this->probabilisticSampling->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->rateLimitingSampling !== null) {
      if (!is_object($this->rateLimitingSampling)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('rateLimitingSampling', TType::STRUCT, 3);
      $xfer += $this->rateLimitingSampling->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->operationSampling !== null) {
      if (!is_object($this->operationSampling)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('operationSampling', TType::STRUCT, 4);
      $xfer += $this->operationSampling->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

