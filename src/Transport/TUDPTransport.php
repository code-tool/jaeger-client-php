<?php

namespace Jaeger\Transport;

use Thrift\Exception\TTransportException;
use Thrift\Transport\TTransport;

class TUDPTransport extends TTransport
{
    private $socket;

    private $host;

    private $port;

    /**
     * TUDPTransport constructor.
     *
     * @param string $host
     * @param int    $port
     */
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    }

    /**
     * @return bool
     */
    public function isOpen()
    {
        return $this->socket !== null;
    }

    public function open()
    {
        @socket_connect($this->socket, $this->host, $this->port);
    }

    public function close()
    {
        socket_close($this->socket);
        $this->socket = null;
    }

    /**
     * @param int $len
     *
     * @return string
     */
    public function read($len)
    {
        return '';
    }

    public function write($buf)
    {
        if (false === $this->isOpen()) {
            throw new TTransportException('Transport is closed');
        }
        $length = strlen($buf);
        while (true) {
            $result = @socket_write($this->socket, $buf);
            if ($result === false) {
                break;
            }
            if ($result >= $length) {
                break;
            }
            $buf = substr($buf, $result);
            $length -= $result;
        }
    }
}
