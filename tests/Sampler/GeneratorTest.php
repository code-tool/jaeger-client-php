<?php

declare(strict_types=1);

namespace Jaeger\Tests\Sampler;

use Jaeger\Sampler\ConstGenerator;
use Jaeger\Sampler\OperationGenerator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConstGenerator::class)]
#[CoversClass(OperationGenerator::class)]
final class GeneratorTest extends TestCase
{
    #[DataProvider('constCases')]
    public function testConstGeneratorShouldIgnoreItsInput(int $traceId, string $operationName): void
    {
        self::assertSame('const', new ConstGenerator()->generate($traceId, $operationName));
    }

    /**
     * @return iterable<string, array{int, string}>
     */
    public static function constCases(): iterable
    {
        yield '📭 zero id, empty operation' => [0, ''];
        yield '✅ populated' => [42, 'an-operation'];
        yield '✅ negative id' => [-42, 'another-operation'];
    }

    #[DataProvider('operationCases')]
    public function testOperationGeneratorShouldKeyOnTheOperationName(
        int $traceId,
        string $operationName,
        string $expected,
    ): void {
        self::assertSame($expected, new OperationGenerator()->generate($traceId, $operationName));
    }

    /**
     * @return iterable<string, array{int, string, string}>
     */
    public static function operationCases(): iterable
    {
        yield '📭 empty operation' => [0, '', 'operation:'];
        yield '✅ populated' => [42, 'an-operation', 'operation:an-operation'];
        yield '✅ id does not affect the key' => [-1, 'an-operation', 'operation:an-operation'];
    }
}
