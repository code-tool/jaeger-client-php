<?php

declare(strict_types=1);

namespace Jaeger\Tests\Codec;

use InvalidArgumentException;
use Jaeger\Codec\CodecRegistry;
use Jaeger\Codec\TextCodec;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CodecRegistry::class)]
final class CodecRegistryTest extends TestCase
{
    public function testShouldStoreAndReturnACodecByKey(): void
    {
        $registry = new CodecRegistry();
        $codec = new TextCodec();

        $registry['text'] = $codec;

        self::assertTrue(isset($registry['text']));
        self::assertSame($codec, $registry['text']);
    }

    public function testShouldReportMissingKeysAsAbsent(): void
    {
        $registry = new CodecRegistry();

        self::assertFalse(isset($registry['nope']));
        self::assertNull($registry['nope']);
    }

    public function testShouldOverwriteAnExistingKey(): void
    {
        $registry = new CodecRegistry();
        $first = new TextCodec();
        $second = new TextCodec();

        $registry['text'] = $first;
        $registry['text'] = $second;

        self::assertSame($second, $registry['text']);
    }

    public function testShouldRemoveAKey(): void
    {
        $registry = new CodecRegistry();
        $registry['text'] = new TextCodec();

        unset($registry['text']);

        self::assertFalse(isset($registry['text']));
        self::assertNull($registry['text']);
    }

    public function testShouldTolerateUnsettingAKeyThatWasNeverRegistered(): void
    {
        $registry = new CodecRegistry();

        unset($registry['nope']);

        self::assertFalse(isset($registry['nope']));
    }

    public function testShouldRejectAValueThatIsNotACodec(): void
    {
        $registry = new CodecRegistry();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIsOrContains('Codec must implement');

        /** @psalm-suppress InvalidArgument — the point of the test is to pass the wrong type */
        $registry['text'] = 'not a codec';
    }

    public function testShouldRejectAppendingWithoutAKey(): void
    {
        $registry = new CodecRegistry();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIsOrContains('appending is not supported');

        $registry[] = new TextCodec();
    }
}
