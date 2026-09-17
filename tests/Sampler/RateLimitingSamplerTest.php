<?php

declare(strict_types=1);

namespace Jaeger\Tests\Sampler;

use Jaeger\Sampler\ConstGenerator;
use Jaeger\Sampler\OperationGenerator;
use Jaeger\Sampler\RateLimitingSampler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(RateLimitingSampler::class)]
final class RateLimitingSamplerTest extends TestCase
{
    protected function setUp(): void
    {
        if (!\extension_loaded('apcu') || !apcu_enabled()) {
            self::markTestSkipped('RateLimitingSampler stores its counters in APCu; run PHP with apc.enable_cli=1');
        }

        apcu_clear_cache();
    }

    #[DataProvider('packingCases')]
    public function testShouldPackATimestampAndCountIntoOneInteger(int $seconds, int $count): void
    {
        $sampler = new RateLimitingSampler(1.0, new ConstGenerator());

        self::assertSame([$seconds, $count], $sampler->spec($sampler->value($seconds, $count)));
    }

    /**
     * @return iterable<string, array{int, int}>
     */
    public static function packingCases(): iterable
    {
        yield '📭 zero' => [0, 0];
        yield '✅ typical' => [1700000000, 7];
        yield '✅ max count in 16 bits' => [1700000000, 0xffff];
        yield '✅ count of one' => [1, 1];
    }

    public function testShouldSampleTheFirstTraceForAKey(): void
    {
        $sampler = new RateLimitingSampler(1.0, new ConstGenerator());

        $result = $sampler->decide(1, 'an-operation', '');

        self::assertTrue($result->isSampled());
        self::assertSame(0x01, $result->getFlags());
    }

    public function testShouldRejectTracesOnceTheRateIsExhausted(): void
    {
        $sampler = new RateLimitingSampler(1.0, new ConstGenerator());

        $sampler->decide(1, 'an-operation', '');

        $second = $sampler->decide(2, 'an-operation', '');

        self::assertFalse($second->isSampled(), 'a rate of 1/s must not admit a second trace in the same second');
    }

    public function testShouldKeepSeparateBudgetsPerOperation(): void
    {
        $sampler = new RateLimitingSampler(1.0, new OperationGenerator());

        $first = $sampler->decide(1, 'operation-a', '');
        $second = $sampler->decide(2, 'operation-b', '');

        self::assertTrue($first->isSampled());
        self::assertTrue($second->isSampled(), 'a different operation has its own budget');
    }

    public function testShouldDescribeItselfThroughTags(): void
    {
        $sampler = new RateLimitingSampler(2.5, new ConstGenerator());

        $tags = [];
        foreach ($sampler->decide(1, 'an-operation', '')->getTags() as $tag) {
            $tags[(string) $tag->key] = $tag->vStr ?? $tag->vBool ?? $tag->vLong;
        }

        self::assertSame('ratelimiting', $tags['sampler.type']);
        self::assertSame('2.5', $tags['sampler.param']);
        self::assertTrue($tags['sampler.decision']);
    }

    /**
     * A generous rate lets later traces through the compare-and-swap path rather than the
     * initial apcu_add, which is a different branch of doDecide().
     */
    public function testShouldKeepAdmittingTracesWhileTheRateAllowsIt(): void
    {
        $sampler = new RateLimitingSampler(1000.0, new ConstGenerator());

        $decisions = [];
        for ($i = 0; $i < 5; $i++) {
            $decisions[] = $sampler->decide($i, 'an-operation', '')->isSampled();
        }

        self::assertSame([true, true, true, true, true], $decisions);
    }

    public function testShouldTagAnAdmittedTraceWithItsCounterKey(): void
    {
        $sampler = new RateLimitingSampler(1000.0, new ConstGenerator());
        $sampler->decide(1, 'an-operation', '');

        $tags = [];
        foreach ($sampler->decide(2, 'an-operation', '')->getTags() as $tag) {
            $tags[(string) $tag->key][] = $tag->vStr ?? $tag->vBool ?? $tag->vLong;
        }

        self::assertContains('const', $tags['sampler.param'], 'the counter key is reported');
        self::assertSame(['ratelimiting'], $tags['sampler.type']);
    }

    public function testShouldAlwaysSampleWhenADebugIdIsSupplied(): void
    {
        $sampler = new RateLimitingSampler(1.0, new ConstGenerator());

        $sampler->decide(1, 'an-operation', '');

        $debug = $sampler->decide(2, 'an-operation', 'debug-id-42');

        self::assertTrue($debug->isSampled());
        self::assertSame(0x03, $debug->getFlags());
    }
}
