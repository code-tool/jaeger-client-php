<?php

declare(strict_types=1);

namespace Jaeger\Tests\Id;

use Jaeger\Id\RandomIntGenerator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RandomIntGenerator::class)]
final class RandomIntGeneratorTest extends TestCase
{
    public function testShouldReturnAnInteger(): void
    {
        self::assertIsInt(new RandomIntGenerator()->next());
    }

    public function testShouldSpanTheWholeSignedRange(): void
    {
        $generator = new RandomIntGenerator();

        for ($i = 0; $i < 100; $i++) {
            $id = $generator->next();
            self::assertGreaterThanOrEqual(PHP_INT_MIN, $id);
            self::assertLessThanOrEqual(PHP_INT_MAX, $id);
        }
    }

    public function testShouldNotRepeatItselfAcrossCalls(): void
    {
        $generator = new RandomIntGenerator();

        $ids = [];
        for ($i = 0; $i < 50; $i++) {
            $ids[] = $generator->next();
        }

        self::assertCount(50, array_unique($ids), 'a 64-bit random id must not collide in 50 draws');
    }

    public function testShouldProduceBothPositiveAndNegativeIds(): void
    {
        $generator = new RandomIntGenerator();

        $signs = [];
        for ($i = 0; $i < 200; $i++) {
            $signs[$generator->next() < 0 ? 'negative' : 'positive'] = true;
        }

        self::assertArrayHasKey('negative', $signs);
        self::assertArrayHasKey('positive', $signs);
    }
}
