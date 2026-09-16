<?php

declare(strict_types=1);

namespace Jaeger\Tests\Sampler;

use Jaeger\Sampler\AdaptiveSampler;
use Jaeger\Sampler\ConstSampler;
use Jaeger\Sampler\SamplerResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AdaptiveSampler::class)]
final class AdaptiveSamplerTest extends TestCase
{
    public function testShouldSampleWhenTheRateLimiterSamples(): void
    {
        $sampler = new AdaptiveSampler(new ConstSampler(true), new ConstSampler(false));

        $result = $sampler->decide(1, 'an-operation', '');

        self::assertTrue($result->isSampled());
        self::assertSame(0x01, $result->getFlags());
    }

    public function testShouldLabelARateLimitedDecisionAsAdaptive(): void
    {
        $sampler = new AdaptiveSampler(new ConstSampler(true), new ConstSampler(false));

        $types = $this->tagValues($sampler->decide(1, 'an-operation', ''), 'sampler.type');

        self::assertSame(['adaptive', 'const'], $types);
    }

    public function testShouldFallBackToTheProbabilisticSampler(): void
    {
        $sampler = new AdaptiveSampler(new ConstSampler(false), new ConstSampler(true));

        self::assertTrue($sampler->decide(1, 'an-operation', '')->isSampled());
    }

    public function testShouldTakeTheFlagsFromWhicheverSamplerAccepted(): void
    {
        $sampler = new AdaptiveSampler(new ConstSampler(false), new ConstSampler(true));

        $result = $sampler->decide(1, 'an-operation', '');

        self::assertTrue($result->isSampled());
        self::assertSame(0x01, $result->getFlags());
    }

    public function testShouldTakeTheTagsFromWhicheverSamplerAccepted(): void
    {
        $sampler = new AdaptiveSampler(new ConstSampler(false), new ConstSampler(true));

        $result = $sampler->decide(1, 'an-operation', '');

        self::assertSame([true], $this->tagValues($result, 'sampler.decision'));
        self::assertSame(['adaptive', 'const'], $this->tagValues($result, 'sampler.type'));
    }

    public function testShouldRejectWhenNeitherSamplerSamples(): void
    {
        $sampler = new AdaptiveSampler(new ConstSampler(false), new ConstSampler(false));

        $result = $sampler->decide(1, 'an-operation', '');

        self::assertFalse($result->isSampled());
        self::assertSame(0, $result->getFlags());
    }

    public function testShouldLabelARejectionAsAdaptive(): void
    {
        $sampler = new AdaptiveSampler(new ConstSampler(false), new ConstSampler(false));

        $result = $sampler->decide(1, 'an-operation', '');

        self::assertSame(['adaptive'], $this->tagValues($result, 'sampler.type'));
        self::assertSame([false], $this->tagValues($result, 'sampler.decision'));
    }

    public function testShouldPassTheDebugIdThroughToTheUnderlyingSamplers(): void
    {
        $sampler = new AdaptiveSampler(new ConstSampler(false), new ConstSampler(false));

        $result = $sampler->decide(1, 'an-operation', 'debug-id-42');

        self::assertTrue($result->isSampled(), 'the rate limiter honours the debug id');
    }

    /**
     * @return list<mixed>
     */
    private function tagValues(SamplerResult $result, string $key): array
    {
        $values = [];
        foreach ($result->getTags() as $tag) {
            if ($key === $tag->key) {
                $values[] = $tag->vStr ?? $tag->vBool ?? $tag->vLong;
            }
        }

        return $values;
    }
}
