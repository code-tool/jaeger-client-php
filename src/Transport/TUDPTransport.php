<?php
declare(strict_types=1);

namespace Jaeger\Transport;

use Thrift\Exception\TTransportException;
use Thrift\Transport\TTransport;

class TUDPTransport extends TTransport
{
    private $socket;

    private $host;

    private $port;

    public function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    }

    public function isOpen(): bool
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

    public function read($len): string
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
