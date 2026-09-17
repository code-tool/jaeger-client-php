<?php

declare(strict_types=1);

namespace Jaeger\Tests\Transport;

use Jaeger\Tests\Fixture\UdpListener;
use Jaeger\Transport\TUDPTransport;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Socket;
use Thrift\Exception\TTransportException;

#[CoversClass(TUDPTransport::class)]
final class TUDPTransportTest extends TestCase
{
    private UdpListener $listener;

    protected function setUp(): void
    {
        $this->listener = new UdpListener();
    }

    protected function tearDown(): void
    {
        $this->listener->close();
    }

    public function testShouldAlwaysReportItselfAsOpen(): void
    {
        self::assertTrue($this->makeTransport()->isOpen());
    }

    public function testShouldTreatOpenAsANoOp(): void
    {
        $transport = $this->makeTransport();

        $transport->open();

        self::assertTrue($transport->isOpen());
    }

    #[DataProvider('payloadCases')]
    public function testShouldDeliverWhatWasWritten(string $payload): void
    {
        $transport = $this->makeTransport();

        $transport->write($payload);
        $transport->flush();

        self::assertSame($payload, $this->listener->receive());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function payloadCases(): iterable
    {
        yield '✅ ascii' => ['hello'];
        yield '✅ binary bytes' => ["\x00\x01\xff\xfe"];
        yield '✅ utf-8' => ['піднімай'];
        yield '✅ one kilobyte' => [str_repeat('x', 1024)];
    }

    public function testShouldConcatenateWritesIntoOneDatagram(): void
    {
        $transport = $this->makeTransport();

        $transport->write('one ');
        $transport->write('two ');
        $transport->write('three');
        $transport->flush();

        self::assertSame('one two three', $this->listener->receive());
    }

    public function testShouldSendNothingWhenTheBufferIsEmpty(): void
    {
        $transport = $this->makeTransport();

        $transport->flush();

        self::assertNull($this->listener->receive(), 'an empty flush must not produce a datagram');
    }

    public function testShouldClearItsBufferAfterFlushing(): void
    {
        $transport = $this->makeTransport();
        $transport->write('once');
        $transport->flush();

        $this->listener->receive();

        $transport->flush();

        self::assertNull($this->listener->receive(), 'a second flush must not resend the payload');
    }

    /**
     * The socket is opened lazily and then reused; PHP 8 returns a Socket object, not a resource.
     */
    public function testShouldReuseOneSocketAcrossFlushes(): void
    {
        $transport = $this->makeTransport();
        $property = new ReflectionProperty(TUDPTransport::class, 'socket');

        $ids = [];
        for ($i = 0; $i < 5; $i++) {
            $transport->write('x');
            $transport->flush();
            $this->listener->receive();
            $socket = $property->getValue($transport);
            self::assertInstanceOf(Socket::class, $socket);
            $ids[] = spl_object_id($socket);
        }

        self::assertCount(1, array_unique($ids));
    }

    public function testShouldNotOpenASocketBeforeTheFirstFlush(): void
    {
        $transport = $this->makeTransport();
        $transport->write('buffered but not sent');

        self::assertNull(new ReflectionProperty(TUDPTransport::class, 'socket')->getValue($transport));
    }

    public function testShouldDropItsSocketOnClose(): void
    {
        $transport = $this->makeTransport();
        $transport->write('x');
        $transport->flush();

        $this->listener->receive();

        $transport->close();

        self::assertNull(new ReflectionProperty(TUDPTransport::class, 'socket')->getValue($transport));
    }

    public function testShouldTolerateClosingBeforeAnythingWasSent(): void
    {
        $transport = $this->makeTransport();

        $transport->close();

        self::assertTrue($transport->isOpen());
    }

    public function testShouldStillSendAfterBeingClosed(): void
    {
        $transport = $this->makeTransport();
        $transport->write('first');
        $transport->flush();

        $this->listener->receive();
        $transport->close();

        $transport->write('second');
        $transport->flush();

        self::assertSame('second', $this->listener->receive());
    }

    public function testShouldRefuseToBeReadFrom(): void
    {
        $this->expectException(TTransportException::class);
        $this->expectExceptionMessageIsOrContains('TUDPTransport is write-only');

        $this->makeTransport()->read(4);
    }

    /**
     * readAll() loops on read() until it has enough bytes; read() must throw rather than spin forever.
     */
    public function testShouldFailFastInsteadOfLoopingForeverOnReadAll(): void
    {
        $this->expectException(TTransportException::class);

        $this->makeTransport()->readAll(4);
    }

    private function makeTransport(): TUDPTransport
    {
        return new TUDPTransport('127.0.0.1', $this->listener->port());
    }
}
