<?php
declare(strict_types=1);

namespace Jaeger\Transport;

use Thrift\Transport\TTransport;

class TUDPTransport extends TTransport
{
    private $host;

    private $port;

    /**
     * @var resource
     */
    private $socket;

    private $buffer = '';

    public function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function isOpen(): bool
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

    public function read($len): string
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
