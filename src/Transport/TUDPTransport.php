<?php
declare(strict_types=1);

namespace Jaeger\Transport;

use Thrift\Exception\TTransportException;
use Thrift\Transport\TTransport;

class TUDPTransport extends TTransport
{
    private string $host;

    private int $port;

    private ?\Socket $socket = null;

    private string $buffer = '';

    public function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function isOpen(): bool
    {
        return true;
    }

    public function open(): void
    {
    }

    public function close(): void
    {
        if (null === $this->socket) {
            return;
        }
        \socket_close($this->socket);
        $this->socket = null;
    }

    public function read(int $len): string
    {
        throw new TTransportException('TUDPTransport is write-only', TTransportException::UNKNOWN);
    }

    public function write(string $buf): void
    {
        $this->buffer .= $buf;
    }

    public function flush(): void
    {
        parent::flush();
        if ('' === $this->buffer) {
            return;
        }
        $this->doWrite($this->buffer);
        $this->buffer = '';
    }

    private function doWrite(string $buf): void
    {
        if (null === ($socket = $this->connect())) {
            return;
        }
        $length = \strlen($buf);
        while (true) {
            if (false === ($result = @\socket_write($socket, $buf))) {
                break;
            }
            if ($result >= $length) {
                break;
            }
            $buf = \substr($buf, $result);
            $length -= $result;
        }
    }

    private function connect(): ?\Socket
    {
        $count = 0;
        while (null === $this->socket && $count < 5) {
            if (false !== ($socket = \socket_create(AF_INET, SOCK_DGRAM, SOL_UDP))) {
                @\socket_connect($socket, $this->host, $this->port);
                $this->socket = $socket;
                break;
            }
            $count++;
            usleep(10);
        }

        return $this->socket;
    }
}
