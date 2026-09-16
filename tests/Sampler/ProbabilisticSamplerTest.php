<?php

declare(strict_types=1);

namespace Jaeger\Tests\Sampler;

use Jaeger\Sampler\ProbabilisticSampler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProbabilisticSampler::class)]
final class ProbabilisticSamplerTest extends TestCase
{
    /**
     * The sampler keeps a trace when abs(traceId) falls inside 0.5 * rate * PHP_INT_MAX.
     */
    #[DataProvider('decisionCases')]
    public function testShouldSampleIdsInsideTheThreshold(float $rate, int $traceId, bool $expectedSampled): void
    {
        $result = new ProbabilisticSampler($rate)->decide($traceId, 'an-operation', '');

        self::assertSame($expectedSampled, $result->isSampled());
    }

    /**
     * @return iterable<string, array{float, int, bool}>
     */
    public static function decisionCases(): iterable
    {
        yield '📭 rate 0 — id 0 still inside' => [0.0, 0, true];
        yield '🚫 rate 0 — any other id is out' => [0.0, 1, false];
        yield '🚫 rate 0 — max id is out' => [0.0, PHP_INT_MAX, false];
        yield '✅ rate 1 — small id' => [1.0, 1, true];
        yield '✅ rate 1 — just inside half of PHP_INT_MAX' => [1.0, (int) (0.4 * (float) PHP_INT_MAX), true];
        yield '🚫 rate 1 — beyond half of PHP_INT_MAX' => [1.0, (int) (0.6 * (float) PHP_INT_MAX), false];
        yield '✅ rate 1 — negative ids use absolute value' => [1.0, -1, true];
        yield '🚫 rate 0.001 — mid-range id is out' => [0.001, (int) (0.5 * (float) PHP_INT_MAX), false];
    }

    public function testShouldFlagASampledTraceAsSampled(): void
    {
        $result = new ProbabilisticSampler(1.0)->decide(1, 'an-operation', '');

        self::assertTrue($result->isSampled());
        self::assertSame(0x01, $result->getFlags());
    }

    public function testShouldNotFlagARejectedTrace(): void
    {
        $result = new ProbabilisticSampler(0.0)->decide(PHP_INT_MAX, 'an-operation', '');

        self::assertFalse($result->isSampled());
        self::assertSame(0x00, $result->getFlags());
    }

    #[DataProvider('tagCases')]
    public function testShouldReportItsRateThroughTags(float $rate, int $traceId, string $expectedParam): void
    {
        $result = new ProbabilisticSampler($rate)->decide($traceId, 'an-operation', '');

        $tags = [];
        foreach ($result->getTags() as $tag) {
            $tags[(string) $tag->key] = $tag->vStr ?? $tag->vBool ?? $tag->vLong;
        }

        self::assertSame('probabilistic', $tags['sampler.type']);
        self::assertSame($expectedParam, $tags['sampler.param']);
    }

    /**
     * @return iterable<string, array{float, int, string}>
     */
    public static function tagCases(): iterable
    {
        yield '✅ sampled' => [1.0, 1, '1'];
        yield '🚫 rejected' => [0.0, PHP_INT_MAX, '0'];
        yield '✅ fractional rate' => [0.5, 1, '0.5'];
    }
}
