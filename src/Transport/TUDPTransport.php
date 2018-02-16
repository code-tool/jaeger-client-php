<?php

namespace Jaeger\Transport;

use Thrift\Transport\TTransport;

class TUDPTransport extends TTransport
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var resource
     */
    private $socket;

    /**
     * @var string
     */
    private $buffer = '';

    /**
     * @param string $host
     * @param int    $port
     */
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function isOpen()
    {
        return true;
    }

    public function open()
    {
    }

    public function close()
    {
        if (null === $this->socket) {
            return;
        }

        \socket_close($this->socket);
        $this->socket = null;
    }

    public function read($len)
    {
        return '';
    }

    public function write($buf)
    {
        $this->buffer .= $buf;
    }

    public function flush()
    {
        if ('' === $this->buffer) {
            return;
        }

        $this->doWrite($this->buffer);
        $this->buffer = '';
    }

    private function doWrite($buf)
    {
        $socket = $this->getConnectedSocket();
        $length = \strlen($buf);
        while (true) {
            if (false === $result = @\socket_write($socket, $buf)) {
                break;
            }
            if ($result >= $length) {
                break;
            }
            $buf = \substr($buf, $result);
            $length -= $result;
        }
    }

    private function getConnectedSocket()
    {
        if (null === $this->socket) {
            if (false !== $socket = \socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
                @\socket_connect($socket, $this->host, $this->port);
            }

            $this->socket = $socket;
        }

        return $this->socket;
    }
}
