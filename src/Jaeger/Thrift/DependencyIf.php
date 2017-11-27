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


interface DependencyIf {
  /**
   * @param string $traceId
   * @return \CodeTool\OpenTracing\Dependencies
   */
  public function getDependenciesForTrace($traceId);
  /**
   * @param \CodeTool\OpenTracing\Dependencies $dependencies
   */
  public function saveDependencies(\CodeTool\OpenTracing\Dependencies $dependencies);
}


