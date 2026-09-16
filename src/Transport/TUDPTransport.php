<?php

declare(strict_types=1);

namespace Jaeger\Transport;

use Socket;
use Thrift\Exception\TTransportException;
use Thrift\Transport\TTransport;

class TUDPTransport extends TTransport
{
    private ?Socket $socket = null;

    private string $buffer = '';

    public function __construct(
        private readonly string $host,
        private readonly int $port,
    ) {}

    public function isOpen(): bool
    {
        return true;
    }

    public function open(): void {}

    public function close(): void
    {
        if (!$this->socket instanceof Socket) {
            return;
        }

        socket_close($this->socket);
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
        if (!($socket = $this->connect()) instanceof Socket) {
            return;
        }

        $length = \strlen($buf);
        while (true) {
            if (false === ($result = @socket_write($socket, $buf))) {
                break;
            }

            if ($result >= $length) {
                break;
            }

            $buf = substr($buf, $result);
            $length -= $result;
        }
    }

    private function connect(): ?Socket
    {
        $count = 0;
        while (!$this->socket instanceof Socket && $count < 5) {
            if (false !== ($socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP))) {
                @socket_connect($socket, $this->host, $this->port);
                $this->socket = $socket;
                break;
            }

            $count++;
            usleep(10);
        }

        return $this->socket;
    }
}
