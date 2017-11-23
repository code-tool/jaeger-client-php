<?php

declare(strict_types=1);

namespace CodeTool\OpenTracing\Transport;

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

    /**
     * Whether this transport is open.
     *
     * @return boolean true if open
     */
    public function isOpen()
    {
        return $this->socket !== null;
    }

    /**
     * Open the transport for reading/writing
     *
     * @throws TTransportException if cannot open
     */
    public function open()
    {
        $ok = socket_connect($this->socket, $this->host, $this->port);
        if ($ok === false) {
            throw new TTransportException('socket_connect failed');
        }
    }

    /**
     * Close the transport.
     */
    public function close()
    {
        socket_close($this->socket);
        $this->socket = null;
    }

    /**
     * Read some data into the array.
     *
     * @param int $len How much to read
     *
     * @return string The data that has been read
     * @throws TTransportException if cannot read any more data
     */
    public function read($len)
    {
        return '';
    }

    /**
     * Writes the given data out.
     *
     * @param string $buf The data to write
     *
     * @throws TTransportException if writing fails
     */
    public function write($buf)
    {
        if (!$this->isOpen()) {
            throw new TTransportException('transport is closed');
        }

        $length = strlen($buf);
        while (true) {
            $result = socket_write($this->socket, $buf);
            if ($result === false) {
                throw new TTransportException('socket_write failed');
            }
            if ($result >= $length) {
                break;
            }
            $buf = substr($buf, $result);
            $length -= $result;
        }
    }
}
