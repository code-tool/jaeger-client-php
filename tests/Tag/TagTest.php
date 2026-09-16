<?php

declare(strict_types=1);

namespace Jaeger\Tests\Tag;

use Jaeger\General\JaegerVersionTag;
use Jaeger\General\PhpBinaryTag;
use Jaeger\General\PhpVersionTag;
use Jaeger\Http\HttpCodeTag;
use Jaeger\Http\HttpMethodTag;
use Jaeger\Http\HttpUriTag;
use Jaeger\Log\ErrorKindTag;
use Jaeger\Log\EventTag;
use Jaeger\Log\LevelTag;
use Jaeger\Log\MessageTag;
use Jaeger\Log\StackTag;
use Jaeger\Process\ProcessSapiTag;
use Jaeger\Sampler\SamplerDecisionTag;
use Jaeger\Sampler\SamplerFlagsTag;
use Jaeger\Sampler\SamplerParamTag;
use Jaeger\Sampler\SamplerTypeTag;
use Jaeger\Sampler\SamplingPriorityTag;
use Jaeger\Tag\AbstractSpanKindTag;
use Jaeger\Tag\AbstractTag;
use Jaeger\Tag\BinaryTag;
use Jaeger\Tag\BoolTag;
use Jaeger\Tag\ComponentTag;
use Jaeger\Tag\DbInstanceTag;
use Jaeger\Tag\DbStatementTag;
use Jaeger\Tag\DbType;
use Jaeger\Tag\DbUser;
use Jaeger\Tag\DebugRequestTag;
use Jaeger\Tag\DoubleTag;
use Jaeger\Tag\ErrorTag;
use Jaeger\Tag\LongTag;
use Jaeger\Tag\MessageBusDestinationTag;
use Jaeger\Tag\OutOfScopeTag;
use Jaeger\Tag\PeerAddressTag;
use Jaeger\Tag\PeerHostnameTag;
use Jaeger\Tag\PeerIpv4Tag;
use Jaeger\Tag\PeerPortTag;
use Jaeger\Tag\PeerServiceTag;
use Jaeger\Tag\SpanKindClientTag;
use Jaeger\Tag\SpanKindConsumerTag;
use Jaeger\Tag\SpanKindProducerTag;
use Jaeger\Tag\SpanKindServerTag;
use Jaeger\Tag\StringTag;
use Jaeger\Thrift\TagType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractTag::class)]
#[CoversClass(AbstractSpanKindTag::class)]
#[CoversClass(StringTag::class)]
#[CoversClass(LongTag::class)]
#[CoversClass(BoolTag::class)]
#[CoversClass(DoubleTag::class)]
#[CoversClass(BinaryTag::class)]
#[CoversClass(ComponentTag::class)]
#[CoversClass(DbInstanceTag::class)]
#[CoversClass(DbStatementTag::class)]
#[CoversClass(DbType::class)]
#[CoversClass(DbUser::class)]
#[CoversClass(DebugRequestTag::class)]
#[CoversClass(ErrorTag::class)]
#[CoversClass(MessageBusDestinationTag::class)]
#[CoversClass(OutOfScopeTag::class)]
#[CoversClass(PeerAddressTag::class)]
#[CoversClass(PeerHostnameTag::class)]
#[CoversClass(PeerIpv4Tag::class)]
#[CoversClass(PeerPortTag::class)]
#[CoversClass(PeerServiceTag::class)]
#[CoversClass(SpanKindClientTag::class)]
#[CoversClass(SpanKindConsumerTag::class)]
#[CoversClass(SpanKindProducerTag::class)]
#[CoversClass(SpanKindServerTag::class)]
#[CoversClass(HttpCodeTag::class)]
#[CoversClass(HttpMethodTag::class)]
#[CoversClass(HttpUriTag::class)]
#[CoversClass(ErrorKindTag::class)]
#[CoversClass(EventTag::class)]
#[CoversClass(LevelTag::class)]
#[CoversClass(MessageTag::class)]
#[CoversClass(StackTag::class)]
#[CoversClass(SamplerDecisionTag::class)]
#[CoversClass(SamplerFlagsTag::class)]
#[CoversClass(SamplerParamTag::class)]
#[CoversClass(SamplerTypeTag::class)]
#[CoversClass(SamplingPriorityTag::class)]
#[CoversClass(JaegerVersionTag::class)]
#[CoversClass(PhpBinaryTag::class)]
#[CoversClass(PhpVersionTag::class)]
#[CoversClass(ProcessSapiTag::class)]
final class TagTest extends TestCase
{
    /**
     * @param class-string<AbstractTag> $tagClass
     * @param list<mixed>               $arguments
     */
    #[DataProvider('tagCases')]
    public function testShouldCarryKeyTypeAndValue(
        string $tagClass,
        array $arguments,
        string $expectedKey,
        int $expectedType,
        string $populatedField,
        mixed $expectedValue,
    ): void {
        $tag = new $tagClass(...$arguments);

        self::assertSame($expectedKey, $tag->key);
        self::assertSame($expectedType, $tag->vType);
        self::assertSame($expectedValue, $tag->{$populatedField});
    }

    /**
     * A tag carries exactly one value; every field that does not match its vType stays null.
     */
    /**
     * @param class-string<AbstractTag> $tagClass
     * @param list<mixed>               $arguments
     */
    #[DataProvider('tagCases')]
    public function testShouldLeaveEveryOtherValueFieldNull(
        string $tagClass,
        array $arguments,
        string $expectedKey,
        int $expectedType,
        string $populatedField,
        mixed $expectedValue,
    ): void {
        $tag = new $tagClass(...$arguments);

        foreach (['vStr', 'vDouble', 'vBool', 'vLong', 'vBinary'] as $field) {
            if ($field === $populatedField) {
                continue;
            }

            self::assertNull($tag->{$field}, \sprintf('%s should be null on %s', $field, $tag::class));
        }
    }

    /**
     * Cases yield a class name plus constructor arguments rather than a built object, so the
     * constructors run inside the test and are counted as covered.
     *
     * @return iterable<string, array{class-string<AbstractTag>, list<mixed>, string, int, string, mixed}>
     */
    public static function tagCases(): iterable
    {
        yield '✅ StringTag' => [StringTag::class, ['a.key', 'a value'], 'a.key', TagType::STRING, 'vStr', 'a value'];
        yield '📭 StringTag — empty value' => [StringTag::class, ['a.key', ''], 'a.key', TagType::STRING, 'vStr', ''];
        yield '✅ LongTag' => [LongTag::class, ['a.key', 42], 'a.key', TagType::LONG, 'vLong', 42];
        yield '📭 LongTag — zero' => [LongTag::class, ['a.key', 0], 'a.key', TagType::LONG, 'vLong', 0];
        yield '✅ LongTag — PHP_INT_MAX' => [LongTag::class, ['a.key', PHP_INT_MAX], 'a.key', TagType::LONG, 'vLong', PHP_INT_MAX];
        yield '✅ BoolTag — true' => [BoolTag::class, ['a.key', true], 'a.key', TagType::BOOL, 'vBool', true];
        yield '📭 BoolTag — false' => [BoolTag::class, ['a.key', false], 'a.key', TagType::BOOL, 'vBool', false];
        yield '✅ DoubleTag' => [DoubleTag::class, ['a.key', 3.5], 'a.key', TagType::DOUBLE, 'vDouble', 3.5];
        yield '📭 DoubleTag — zero' => [DoubleTag::class, ['a.key', 0.0], 'a.key', TagType::DOUBLE, 'vDouble', 0.0];
        yield '✅ BinaryTag — non-UTF-8 bytes' => [BinaryTag::class, ['a.key', "\x00\x01\xff"], 'a.key', TagType::BINARY, 'vBinary', "\x00\x01\xff"];

        yield '✅ ComponentTag' => [ComponentTag::class, ['db'], 'component', TagType::STRING, 'vStr', 'db'];
        yield '✅ DbInstanceTag' => [DbInstanceTag::class, ['main'], 'db.instance', TagType::STRING, 'vStr', 'main'];
        yield '✅ DbStatementTag' => [DbStatementTag::class, ['SELECT 1'], 'db.statement', TagType::STRING, 'vStr', 'SELECT 1'];
        yield '✅ DbType' => [DbType::class, ['mysql'], 'db.type', TagType::STRING, 'vStr', 'mysql'];
        yield '✅ DbUser' => [DbUser::class, ['root'], 'db.user', TagType::STRING, 'vStr', 'root'];
        yield '✅ DebugRequestTag' => [DebugRequestTag::class, ['abc123'], 'debug', TagType::STRING, 'vStr', 'abc123'];
        yield '✅ ErrorTag' => [ErrorTag::class, [], 'error', TagType::BOOL, 'vBool', true];
        yield '✅ MessageBusDestinationTag' => [MessageBusDestinationTag::class, ['queue'], 'message_bus.destination', TagType::STRING, 'vStr', 'queue'];
        yield '✅ OutOfScopeTag' => [OutOfScopeTag::class, [], 'scope.missing', TagType::BOOL, 'vBool', true];
        yield '✅ PeerAddressTag' => [PeerAddressTag::class, ['1.2.3.4:80'], 'peer.address', TagType::STRING, 'vStr', '1.2.3.4:80'];
        yield '✅ PeerHostnameTag' => [PeerHostnameTag::class, ['example.test'], 'peer.hostname', TagType::STRING, 'vStr', 'example.test'];
        yield '✅ PeerIpv4Tag' => [PeerIpv4Tag::class, ['1.2.3.4'], 'peer.ipv4', TagType::STRING, 'vStr', '1.2.3.4'];
        yield '✅ PeerPortTag' => [PeerPortTag::class, [8080], 'peer.port', TagType::LONG, 'vLong', 8080];
        yield '✅ PeerServiceTag' => [PeerServiceTag::class, ['svc'], 'peer.service', TagType::STRING, 'vStr', 'svc'];

        yield '✅ SpanKindClientTag' => [SpanKindClientTag::class, [], 'span.kind', TagType::STRING, 'vStr', 'client'];
        yield '✅ SpanKindServerTag' => [SpanKindServerTag::class, [], 'span.kind', TagType::STRING, 'vStr', 'server'];
        yield '✅ SpanKindProducerTag' => [SpanKindProducerTag::class, [], 'span.kind', TagType::STRING, 'vStr', 'producer'];
        yield '✅ SpanKindConsumerTag' => [SpanKindConsumerTag::class, [], 'span.kind', TagType::STRING, 'vStr', 'consumer'];

        yield '✅ HttpCodeTag' => [HttpCodeTag::class, [404], 'http.status_code', TagType::LONG, 'vLong', 404];
        yield '✅ HttpMethodTag' => [HttpMethodTag::class, ['POST'], 'http.method', TagType::STRING, 'vStr', 'POST'];
        yield '✅ HttpUriTag' => [HttpUriTag::class, ['/a/b'], 'http.url', TagType::STRING, 'vStr', '/a/b'];

        yield '✅ ErrorKindTag' => [ErrorKindTag::class, ['RuntimeException'], 'error.kind', TagType::STRING, 'vStr', 'RuntimeException'];
        yield '✅ EventTag' => [EventTag::class, ['error'], 'event', TagType::STRING, 'vStr', 'error'];
        yield '✅ LevelTag' => [LevelTag::class, ['warning'], 'level', TagType::STRING, 'vStr', 'warning'];
        yield '✅ MessageTag' => [MessageTag::class, ['boom'], 'message', TagType::STRING, 'vStr', 'boom'];
        yield '✅ StackTag' => [StackTag::class, ['#0 main()'], 'stack', TagType::STRING, 'vStr', '#0 main()'];

        yield '✅ SamplerDecisionTag' => [SamplerDecisionTag::class, [true], 'sampler.decision', TagType::BOOL, 'vBool', true];
        yield '✅ SamplerFlagsTag' => [SamplerFlagsTag::class, [0x01], 'sampler.flags', TagType::LONG, 'vLong', 1];
        yield '✅ SamplerParamTag' => [SamplerParamTag::class, ['0.5'], 'sampler.param', TagType::STRING, 'vStr', '0.5'];
        yield '✅ SamplerTypeTag' => [SamplerTypeTag::class, ['const'], 'sampler.type', TagType::STRING, 'vStr', 'const'];
        yield '✅ SamplingPriorityTag' => [SamplingPriorityTag::class, [1], 'sampling.priority', TagType::LONG, 'vLong', 1];

        yield '✅ JaegerVersionTag' => [JaegerVersionTag::class, [], 'jaeger.version', TagType::STRING, 'vStr', 'PHP'];
        yield '✅ PhpBinaryTag' => [PhpBinaryTag::class, [], 'php.bin', TagType::STRING, 'vStr', PHP_BINARY];
        yield '✅ PhpVersionTag' => [PhpVersionTag::class, [], 'php.version', TagType::STRING, 'vStr', PHP_VERSION];
        yield '✅ ProcessSapiTag' => [ProcessSapiTag::class, [], 'process.sapi', TagType::STRING, 'vStr', PHP_SAPI];
    }
}
