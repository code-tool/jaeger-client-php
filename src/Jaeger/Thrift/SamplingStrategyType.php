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


final class SamplingStrategyType {
  const PROBABILISTIC = 0;
  const RATE_LIMITING = 1;
  static public $__names = array(
    0 => 'PROBABILISTIC',
    1 => 'RATE_LIMITING',
  );
}

