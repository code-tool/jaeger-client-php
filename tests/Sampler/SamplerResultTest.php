<?php

declare(strict_types=1);

namespace Jaeger\Tests\Sampler;

use Jaeger\Sampler\SamplerResult;
use Jaeger\Sampler\SamplerTypeTag;
use Jaeger\Tag\StringTag;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(SamplerResult::class)]
final class SamplerResultTest extends TestCase
{
    #[DataProvider('resultCases')]
    public function testShouldExposeWhatItWasConstructedWith(
        SamplerResult $result,
        bool $expectedSampled,
        int $expectedFlags,
        int $expectedTagCount,
    ): void {
        self::assertSame($expectedSampled, $result->isSampled());
        self::assertSame($expectedFlags, $result->getFlags());
        self::assertCount($expectedTagCount, $result->getTags());
    }

    /**
     * @return iterable<string, array{SamplerResult, bool, int, int}>
     */
    public static function resultCases(): iterable
    {
        yield '📭 rejected, no tags' => [new SamplerResult(false, 0), false, 0, 0];
        yield '✅ sampled with flags' => [new SamplerResult(true, 0x03), true, 0x03, 0];
        yield '✅ sampled with tags' => [
            new SamplerResult(true, 0x01, [new SamplerTypeTag('const'), new StringTag('a', 'b')]),
            true,
            0x01,
            2,
        ];
    }

    public function testShouldDefaultToAnEmptyTagList(): void
    {
        self::assertSame([], new SamplerResult(true, 1)->getTags());
    }
}
