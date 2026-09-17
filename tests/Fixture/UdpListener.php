<?php

declare(strict_types=1);

namespace Jaeger\Tests\Fixture;

use RuntimeException;
use Socket;

/**
 * A bound UDP socket on a free loopback port, used to observe what a transport actually sends.
 */
final readonly class UdpListener
{
    private Socket $socket;

    private int $port;

    public function __construct()
    {
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if (false === $socket) {
            throw new RuntimeException('Cannot create a UDP listener socket');
        }

        if (false === socket_bind($socket, '127.0.0.1', 0)) {
            throw new RuntimeException('Cannot bind the UDP listener to loopback');
        }

        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 0, 'usec' => 300000]);
        socket_getsockname($socket, $address, $port);

        $this->socket = $socket;
        $this->port = $port;
    }

    public function port(): int
    {
        return $this->port;
    }

    /**
     * Blocks for at most 300ms; returns null when nothing arrives.
     */
    public function receive(): ?string
    {
        $buffer = '';
        $from = '';
        $fromPort = 0;
        $read = socket_recvfrom($this->socket, $buffer, 65535, 0, $from, $fromPort);

        return false === $read ? null : $buffer;
    }

    public function close(): void
    {
        socket_close($this->socket);
    }
}
