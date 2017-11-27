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


final class Constant extends \Thrift\Type\TConstant {
  static protected $CLIENT_SEND;
  static protected $CLIENT_RECV;
  static protected $SERVER_SEND;
  static protected $SERVER_RECV;
  static protected $WIRE_SEND;
  static protected $WIRE_RECV;
  static protected $CLIENT_SEND_FRAGMENT;
  static protected $CLIENT_RECV_FRAGMENT;
  static protected $SERVER_SEND_FRAGMENT;
  static protected $SERVER_RECV_FRAGMENT;
  static protected $LOCAL_COMPONENT;
  static protected $CLIENT_ADDR;
  static protected $SERVER_ADDR;

  static protected function init_CLIENT_SEND() {
    return     /**
     * The client sent ("cs") a request to a server. There is only one send per
     * span. For example, if there's a transport error, each attempt can be logged
     * as a WIRE_SEND annotation.
     * 
     * If chunking is involved, each chunk could be logged as a separate
     * CLIENT_SEND_FRAGMENT in the same span.
     * 
     * Annotation.host is not the server. It is the host which logged the send
     * event, almost always the client. When logging CLIENT_SEND, instrumentation
     * should also log the SERVER_ADDR.
     */
"cs";
  }

  static protected function init_CLIENT_RECV() {
    return     /**
     * The client received ("cr") a response from a server. There is only one
     * receive per span. For example, if duplicate responses were received, each
     * can be logged as a WIRE_RECV annotation.
     * 
     * If chunking is involved, each chunk could be logged as a separate
     * CLIENT_RECV_FRAGMENT in the same span.
     * 
     * Annotation.host is not the server. It is the host which logged the receive
     * event, almost always the client. The actual endpoint of the server is
     * recorded separately as SERVER_ADDR when CLIENT_SEND is logged.
     */
"cr";
  }

  static protected function init_SERVER_SEND() {
    return     /**
     * The server sent ("ss") a response to a client. There is only one response
     * per span. If there's a transport error, each attempt can be logged as a
     * WIRE_SEND annotation.
     * 
     * Typically, a trace ends with a server send, so the last timestamp of a trace
     * is often the timestamp of the root span's server send.
     * 
     * If chunking is involved, each chunk could be logged as a separate
     * SERVER_SEND_FRAGMENT in the same span.
     * 
     * Annotation.host is not the client. It is the host which logged the send
     * event, almost always the server. The actual endpoint of the client is
     * recorded separately as CLIENT_ADDR when SERVER_RECV is logged.
     */
"ss";
  }

  static protected function init_SERVER_RECV() {
    return     /**
     * The server received ("sr") a request from a client. There is only one
     * request per span.  For example, if duplicate responses were received, each
     * can be logged as a WIRE_RECV annotation.
     * 
     * Typically, a trace starts with a server receive, so the first timestamp of a
     * trace is often the timestamp of the root span's server receive.
     * 
     * If chunking is involved, each chunk could be logged as a separate
     * SERVER_RECV_FRAGMENT in the same span.
     * 
     * Annotation.host is not the client. It is the host which logged the receive
     * event, almost always the server. When logging SERVER_RECV, instrumentation
     * should also log the CLIENT_ADDR.
     */
"sr";
  }

  static protected function init_WIRE_SEND() {
    return     /**
     * Optionally logs an attempt to send a message on the wire. Multiple wire send
     * events could indicate network retries. A lag between client or server send
     * and wire send might indicate queuing or processing delay.
     */
"ws";
  }

  static protected function init_WIRE_RECV() {
    return     /**
     * Optionally logs an attempt to receive a message from the wire. Multiple wire
     * receive events could indicate network retries. A lag between wire receive
     * and client or server receive might indicate queuing or processing delay.
     */
"wr";
  }

  static protected function init_CLIENT_SEND_FRAGMENT() {
    return     /**
     * Optionally logs progress of a (CLIENT_SEND, WIRE_SEND). For example, this
     * could be one chunk in a chunked request.
     */
"csf";
  }

  static protected function init_CLIENT_RECV_FRAGMENT() {
    return     /**
     * Optionally logs progress of a (CLIENT_RECV, WIRE_RECV). For example, this
     * could be one chunk in a chunked response.
     */
"crf";
  }

  static protected function init_SERVER_SEND_FRAGMENT() {
    return     /**
     * Optionally logs progress of a (SERVER_SEND, WIRE_SEND). For example, this
     * could be one chunk in a chunked response.
     */
"ssf";
  }

  static protected function init_SERVER_RECV_FRAGMENT() {
    return     /**
     * Optionally logs progress of a (SERVER_RECV, WIRE_RECV). For example, this
     * could be one chunk in a chunked request.
     */
"srf";
  }

  static protected function init_LOCAL_COMPONENT() {
    return     /**
     * The value of "lc" is the component or namespace of a local span.
     * 
     * BinaryAnnotation.host adds service context needed to support queries.
     * 
     * Local Component("lc") supports three key features: flagging, query by
     * service and filtering Span.name by namespace.
     * 
     * While structurally the same, local spans are fundamentally different than
     * RPC spans in how they should be interpreted. For example, zipkin v1 tools
     * center on RPC latency and service graphs. Root local-spans are neither
     * indicative of critical path RPC latency, nor have impact on the shape of a
     * service graph. By flagging with "lc", tools can special-case local spans.
     * 
     * Zipkin v1 Spans are unqueryable unless they can be indexed by service name.
     * The only path to a service name is by (Binary)?Annotation.host.serviceName.
     * By logging "lc", a local span can be queried even if no other annotations
     * are logged.
     * 
     * The value of "lc" is the namespace of Span.name. For example, it might be
     * "finatra2", for a span named "bootstrap". "lc" allows you to resolves
     * conflicts for the same Span.name, for example "finatra/bootstrap" vs
     * "finch/bootstrap". Using local component, you'd search for spans named
     * "bootstrap" where "lc=finch"
     */
"lc";
  }

  static protected function init_CLIENT_ADDR() {
    return     /**
     * Indicates a client address ("ca") in a span. Most likely, there's only one.
     * Multiple addresses are possible when a client changes its ip or port within
     * a span.
     */
"ca";
  }

  static protected function init_SERVER_ADDR() {
    return     /**
     * Indicates a server address ("sa") in a span. Most likely, there's only one.
     * Multiple addresses are possible when a client is redirected, or fails to a
     * different server ip or port.
     */
"sa";
  }
}

