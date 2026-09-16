<?php

declare(strict_types=1);

namespace Jaeger\Tests\Codec;

use Jaeger\Codec\TextCodec;
use Jaeger\Span\Context\SpanContext;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(TextCodec::class)]
final class TextCodecTest extends TestCase
{
    #[DataProvider('encodingCases')]
    public function testShouldEncodeContextToWireFormat(SpanContext $context, string $expected): void
    {
        self::assertSame($expected, new TextCodec()->encode($context));
    }

    #[DataProvider('encodingCases')]
    public function testShouldDecodeWireFormatBackToContext(SpanContext $expected, string $encoded): void
    {
        $decoded = new TextCodec()->decode($encoded);

        self::assertInstanceOf(SpanContext::class, $decoded);
        self::assertSame($expected->getTraceIdHigh(), $decoded->getTraceIdHigh());
        self::assertSame($expected->getTraceIdLow(), $decoded->getTraceIdLow());
        self::assertSame($expected->getSpanId(), $decoded->getSpanId());
        self::assertSame($expected->getParentId(), $decoded->getParentId());
        self::assertSame($expected->getFlags(), $decoded->getFlags());
    }

    /**
     * @return iterable<string, array{SpanContext, string}>
     */
    public static function encodingCases(): iterable
    {
        yield '📭 all zeroes' => [new SpanContext(0, 0, 0, 0, 0), '0:0:0:0'];
        yield '✅ 64-bit trace id' => [new SpanContext(0, 0x1a, 0x2b, 0x3c, 1), '1a:2b:3c:1'];
        yield '✅ 64-bit trace id, negative' => [new SpanContext(0, -1, 0x2b, 0x3c, 1), 'ffffffffffffffff:2b:3c:1'];
        // A 128-bit trace id pads the low half to a full 16 hex digits so decode() can split it back out.
        yield '✅ 128-bit trace id' => [
            new SpanContext(0xaa, 0xbb, 0xcc, 0xdd, 3),
            'aa00000000000000bb:cc:dd:3',
        ];
        yield '✅ 128-bit trace id, full low half' => [
            new SpanContext(0xaa, 0x7abcdef012345678, 0xcc, 0xdd, 3),
            'aa7abcdef012345678:cc:dd:3',
        ];
    }

    #[DataProvider('undecodableCases')]
    public function testShouldRejectInputItCannotDecode(mixed $data): void
    {
        self::assertNull(new TextCodec()->decode($data));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function undecodableCases(): iterable
    {
        yield '🚫 not a string' => [42];
        yield '🚫 null' => [null];
        yield '🚫 array' => [['1', '2', '3', '4']];
        yield '🚫 too few segments' => ['1:2:3'];
        yield '🚫 too many segments' => ['1:2:3:4:5'];
        yield '🚫 empty string' => [''];
    }

    public function testShouldRoundTripA128BitTraceIdThroughEncodeAndDecode(): void
    {
        $codec = new TextCodec();
        $original = new SpanContext(0x1122334455667788, 0x7abcdef012345678, 0x1234, 0x5678, 1);

        $decoded = $codec->decode($codec->encode($original));

        self::assertInstanceOf(SpanContext::class, $decoded);
        self::assertSame($original->getTraceIdHigh(), $decoded->getTraceIdHigh());
        self::assertSame($original->getTraceIdLow(), $decoded->getTraceIdLow());
    }

    public function testShouldRoundTripAShortHighAndLowHalf(): void
    {
        $codec = new TextCodec();
        $original = new SpanContext(0xaa, 0xbb, 0xcc, 0xdd, 3);

        $decoded = $codec->decode($codec->encode($original));

        self::assertInstanceOf(SpanContext::class, $decoded);
        self::assertSame(0xaa, $decoded->getTraceIdHigh());
        self::assertSame(0xbb, $decoded->getTraceIdLow());
    }

    public function testShouldNotPadA64BitTraceId(): void
    {
        self::assertSame('1a:0:0:0', new TextCodec()->encode(new SpanContext(0, 0x1a, 0, 0, 0)));
    }

    public function testShouldConvertHexToSignedInt64(): void
    {
        $codec = new TextCodec();

        self::assertSame(0, $codec->convertInt64('0'));
        self::assertSame(255, $codec->convertInt64('ff'));
        self::assertSame(-1, $codec->convertInt64('ffffffffffffffff'));
        self::assertSame(PHP_INT_MAX, $codec->convertInt64('7fffffffffffffff'));
    }

    public function testShouldSplitHexIntoHighAndLowHalves(): void
    {
        self::assertSame([0, 255], new TextCodec()->convertInt128('ff'));
        self::assertSame([1, 0], new TextCodec()->convertInt128('10000000000000000'));
    }
}
