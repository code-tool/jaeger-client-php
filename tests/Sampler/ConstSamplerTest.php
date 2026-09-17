<?php

declare(strict_types=1);

namespace Jaeger\Tests\Sampler;

use Jaeger\Sampler\AbstractSampler;
use Jaeger\Sampler\ConstSampler;
use Jaeger\Sampler\SamplerResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConstSampler::class)]
#[CoversClass(AbstractSampler::class)]
final class ConstSamplerTest extends TestCase
{
    #[DataProvider('decisionCases')]
    public function testShouldDecideConstantlyRegardlessOfTraceId(
        bool $debugEnabled,
        int $traceId,
        bool $expectedSampled,
        int $expectedFlags,
    ): void {
        $result = new ConstSampler($debugEnabled)->decide($traceId, 'an-operation', '');

        self::assertSame($expectedSampled, $result->isSampled());
        self::assertSame($expectedFlags, $result->getFlags());
    }

    /**
     * @return iterable<string, array{bool, int, bool, int}>
     */
    public static function decisionCases(): iterable
    {
        yield '✅ enabled — zero id' => [true, 0, true, 0x01];
        yield '✅ enabled — max id' => [true, PHP_INT_MAX, true, 0x01];
        yield '✅ enabled — min id' => [true, PHP_INT_MIN, true, 0x01];
        yield '📭 disabled — zero id' => [false, 0, false, 0];
        yield '📭 disabled — max id' => [false, PHP_INT_MAX, false, 0];
    }

    #[DataProvider('tagCases')]
    public function testShouldDescribeItselfThroughTags(bool $debugEnabled, string $expectedParam): void
    {
        $result = new ConstSampler($debugEnabled)->decide(1, 'an-operation', '');

        $tags = [];
        foreach ($result->getTags() as $tag) {
            $tags[(string) $tag->key] = $tag->vStr ?? $tag->vBool ?? $tag->vLong;
        }

        self::assertSame('const', $tags['sampler.type']);
        self::assertSame($expectedParam, $tags['sampler.param']);
        self::assertSame($debugEnabled, $tags['sampler.decision']);
    }

    /**
     * @return iterable<string, array{bool, string}>
     */
    public static function tagCases(): iterable
    {
        yield '✅ enabled' => [true, 'True'];
        yield '📭 disabled' => [false, 'False'];
    }

    public function testShouldAlwaysSampleWhenADebugIdIsSupplied(): void
    {
        $result = new ConstSampler(false)->decide(1, 'an-operation', 'debug-id-42');

        self::assertTrue($result->isSampled());
        self::assertSame(0x03, $result->getFlags());
    }

    public function testShouldTagADebugDecisionWithTheSuppliedDebugId(): void
    {
        $result = new ConstSampler(false)->decide(1, 'an-operation', 'debug-id-42');

        $tags = [];
        foreach ($result->getTags() as $tag) {
            $tags[(string) $tag->key] = $tag->vStr ?? $tag->vBool ?? $tag->vLong;
        }

        self::assertSame('debug', $tags['sampler.type']);
        self::assertSame('debug-id-42', $tags['debug']);
        self::assertSame(1, $tags['sampling.priority']);
        self::assertTrue($tags['sampler.decision']);
    }

    public function testShouldReturnASamplerResult(): void
    {
        self::assertInstanceOf(SamplerResult::class, new ConstSampler(true)->decide(1, 'op', ''));
    }
}
