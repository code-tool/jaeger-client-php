<?php

declare(strict_types=1);

namespace Jaeger\Tests\Span\Context;

use Jaeger\Span\Context\SpanContext;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(SpanContext::class)]
final class SpanContextTest extends TestCase
{
    #[DataProvider('contextCases')]
    public function testShouldExposeItsIdentifiers(
        SpanContext $context,
        int $traceIdHigh,
        int $traceIdLow,
        int $spanId,
        int $parentId,
        int $flags,
    ): void {
        self::assertSame($traceIdHigh, $context->getTraceIdHigh());
        self::assertSame($traceIdLow, $context->getTraceIdLow());
        self::assertSame($traceIdLow, $context->getTraceId(), 'getTraceId() returns the low half');
        self::assertSame($spanId, $context->getSpanId());
        self::assertSame($parentId, $context->getParentId());
        self::assertSame($flags, $context->getFlags());
    }

    /**
     * @return iterable<string, array{SpanContext, int, int, int, int, int}>
     */
    public static function contextCases(): iterable
    {
        yield '📭 all zeroes' => [new SpanContext(0, 0, 0, 0), 0, 0, 0, 0, 0];
        yield '✅ populated' => [new SpanContext(1, 2, 3, 4, 5), 1, 2, 3, 4, 5];
        yield '✅ extreme ids' => [
            new SpanContext(PHP_INT_MAX, PHP_INT_MIN, PHP_INT_MAX, PHP_INT_MIN, 3),
            PHP_INT_MAX, PHP_INT_MIN, PHP_INT_MAX, PHP_INT_MIN, 3,
        ];
    }

    #[DataProvider('flagCases')]
    public function testShouldDecodeTheFlagBits(int $flags, bool $sampled, bool $debug): void
    {
        $context = new SpanContext(0, 0, 0, 0, $flags);

        self::assertSame($sampled, $context->isSampled());
        self::assertSame($debug, $context->isDebug());
    }

    /**
     * @return iterable<string, array{int, bool, bool}>
     */
    public static function flagCases(): iterable
    {
        yield '📭 no flags' => [0x00, false, false];
        yield '✅ sampled' => [0x01, true, false];
        yield '✅ debug' => [0x02, false, true];
        yield '✅ sampled and debug' => [0x03, true, true];
        yield '✅ unrelated high bits are ignored' => [0xf0, false, false];
    }

    public function testShouldDefaultFlagsAndBaggageToEmpty(): void
    {
        $context = new SpanContext(1, 2, 3, 4);

        self::assertSame(0, $context->getFlags());
        self::assertSame([], $context->getBaggage());
    }

    public function testShouldReturnACopyWhenAddingBaggageInsteadOfMutating(): void
    {
        $original = new SpanContext(1, 2, 3, 4);

        $copy = $original->withItem('key', 'value');

        self::assertNotSame($original, $copy);
        self::assertSame([], $original->getBaggage(), 'the original must stay untouched');
        self::assertSame(['key' => 'value'], $copy->getBaggage());
    }

    public function testShouldReturnACopyWhenRemovingBaggage(): void
    {
        $original = new SpanContext(1, 2, 3, 4)->withItem('key', 'value');

        $copy = $original->withoutItem('key');

        self::assertNotSame($original, $copy);
        self::assertSame(['key' => 'value'], $original->getBaggage());
        self::assertSame([], $copy->getBaggage());
    }

    public function testShouldKeepIdentifiersWhenCopyingBaggage(): void
    {
        $copy = new SpanContext(1, 2, 3, 4, 5)->withItem('key', 'value');

        self::assertSame(1, $copy->getTraceIdHigh());
        self::assertSame(2, $copy->getTraceIdLow());
        self::assertSame(3, $copy->getSpanId());
        self::assertSame(4, $copy->getParentId());
        self::assertSame(5, $copy->getFlags());
    }

    #[DataProvider('baggageValueCases')]
    public function testShouldStoreAnyBaggageValue(mixed $value): void
    {
        self::assertSame($value, new SpanContext(1, 2, 3, 4)->withItem('key', $value)->getItem('key'));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function baggageValueCases(): iterable
    {
        yield '✅ string' => ['a value'];
        yield '✅ int' => [42];
        yield '✅ float' => [4.2];
        yield '✅ bool' => [true];
        yield '📭 null' => [null];
        yield '✅ array' => [['a', 'b']];
    }

    public function testShouldFallBackToTheDefaultForAnUnknownBaggageKey(): void
    {
        $context = new SpanContext(1, 2, 3, 4);

        self::assertNull($context->getItem('missing'));
        self::assertSame('fallback', $context->getItem('missing', 'fallback'));
    }

    public function testShouldPreferAStoredNullOverTheDefault(): void
    {
        $context = new SpanContext(1, 2, 3, 4)->withItem('key', null);

        self::assertNull($context->getItem('key', 'fallback'));
    }

    public function testShouldTolerateRemovingAKeyThatWasNeverSet(): void
    {
        self::assertSame([], new SpanContext(1, 2, 3, 4)->withoutItem('missing')->getBaggage());
    }

    public function testShouldIterateOverItsBaggage(): void
    {
        $context = new SpanContext(1, 2, 3, 4)->withItem('a', 1)->withItem('b', 2);

        self::assertSame(['a' => 1, 'b' => 2], iterator_to_array($context));
    }
}
