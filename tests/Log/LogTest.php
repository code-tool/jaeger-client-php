<?php

declare(strict_types=1);

namespace Jaeger\Tests\Log;

use Jaeger\Log\AbstractLog;
use Jaeger\Log\ErrorLog;
use Jaeger\Log\ErrorObjectTag;
use Jaeger\Log\UserLog;
use Jaeger\Tag\StringTag;
use JsonSerializable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractLog::class)]
#[CoversClass(ErrorLog::class)]
#[CoversClass(UserLog::class)]
#[CoversClass(ErrorObjectTag::class)]
final class LogTest extends TestCase
{
    #[DataProvider('logCases')]
    public function testShouldCarryTheExpectedFields(AbstractLog $log, array $expectedFields): void
    {
        $fields = [];
        foreach ($log->fields ?? [] as $tag) {
            $fields[(string) $tag->key] = $tag->vStr;
        }

        self::assertSame($expectedFields, $fields);
    }

    /**
     * @return iterable<string, array{AbstractLog, array<string, string>}>
     */
    public static function logCases(): iterable
    {
        yield '✅ ErrorLog' => [
            new ErrorLog('it broke', '#0 main()', 1_700_000_000_000_000),
            ['event' => 'error', 'message' => 'it broke', 'stack' => '#0 main()'],
        ];
        yield '📭 ErrorLog — empty strings' => [
            new ErrorLog('', '', 1),
            ['event' => 'error', 'message' => '', 'stack' => ''],
        ];
        yield '✅ UserLog' => [
            new UserLog('user.created', 'info', 'a message', 1_700_000_000_000_000),
            ['event' => 'user.created', 'level' => 'info', 'message' => 'a message'],
        ];
    }

    #[DataProvider('timestampCases')]
    public function testShouldUseAnExplicitTimestampWhenGiven(AbstractLog $log, int $expected): void
    {
        self::assertSame($expected, $log->timestamp);
    }

    /**
     * @return iterable<string, array{AbstractLog, int}>
     */
    public static function timestampCases(): iterable
    {
        yield '✅ ErrorLog' => [new ErrorLog('m', 's', 1_700_000_000_000_000), 1_700_000_000_000_000];
        yield '✅ UserLog' => [new UserLog('e', 'l', 'm', 42), 42];
    }

    public function testShouldStampTheCurrentTimeWhenNoTimestampIsGiven(): void
    {
        $before = (int) round(microtime(true) * 1000000.0);

        $log = new ErrorLog('it broke', '#0 main()');

        $after = (int) round(microtime(true) * 1000000.0);
        self::assertGreaterThanOrEqual($before, $log->timestamp);
        self::assertLessThanOrEqual($after, $log->timestamp);
    }

    public function testShouldTreatTimestampZeroAsMeaningNow(): void
    {
        $log = new UserLog('e', 'l', 'm', 0);

        self::assertGreaterThan(0, $log->timestamp);
    }

    public function testShouldAcceptAnEmptyFieldList(): void
    {
        $log = new class ([], 42) extends AbstractLog {};

        self::assertSame([], $log->fields);
        self::assertSame(42, $log->timestamp);
    }

    public function testShouldKeepArbitraryFieldTags(): void
    {
        $tag = new StringTag('custom', 'value');

        $log = new class ([$tag], 1) extends AbstractLog {};

        self::assertSame([$tag], $log->fields);
    }

    public function testShouldSerialiseAJsonObjectIntoAnErrorObjectTag(): void
    {
        $value = new class implements JsonSerializable {
            public function jsonSerialize(): array
            {
                return ['code' => 500, 'reason' => 'boom'];
            }
        };

        $tag = new ErrorObjectTag($value);

        self::assertSame('error.object', $tag->key);
        self::assertSame('{"code":500,"reason":"boom"}', $tag->vStr);
    }

    public function testShouldFallBackToAnEmptyStringWhenTheObjectCannotBeEncoded(): void
    {
        $value = new class implements JsonSerializable {
            public function jsonSerialize(): string
            {
                return "\xB1\x31";
            }
        };

        self::assertSame('', new ErrorObjectTag($value)->vStr);
    }
}
