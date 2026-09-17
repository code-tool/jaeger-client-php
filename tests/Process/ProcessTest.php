<?php

declare(strict_types=1);

namespace Jaeger\Tests\Process;

use Jaeger\General\JaegerHostnameTag;
use Jaeger\Process\AbstractProcess;
use Jaeger\Process\CliProcess;
use Jaeger\Process\FpmProcess;
use Jaeger\Process\InternalServerProcess;
use Jaeger\Process\ProcessGidTag;
use Jaeger\Process\ProcessIpTag;
use Jaeger\Process\ProcessPidTag;
use Jaeger\Process\ProcessUidTag;
use Jaeger\Tag\ComponentTag;
use Jaeger\Thrift\Tag;
use Jaeger\Thrift\TagType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractProcess::class)]
#[CoversClass(CliProcess::class)]
#[CoversClass(FpmProcess::class)]
#[CoversClass(InternalServerProcess::class)]
#[CoversClass(ProcessIpTag::class)]
#[CoversClass(JaegerHostnameTag::class)]
#[CoversClass(ProcessPidTag::class)]
#[CoversClass(ProcessUidTag::class)]
#[CoversClass(ProcessGidTag::class)]
final class ProcessTest extends TestCase
{
    /**
     * @param class-string<AbstractProcess> $processClass
     */
    #[DataProvider('processCases')]
    public function testShouldCarryItsServiceName(string $processClass): void
    {
        self::assertSame('a-service', new $processClass('a-service')->serviceName);
    }

    /**
     * Every process describes its runtime, so a trace can be attributed to a host and PHP build.
     */
    /**
     * @param class-string<AbstractProcess> $processClass
     */
    #[DataProvider('processCases')]
    public function testShouldDescribeItsRuntime(string $processClass): void
    {
        $process = new $processClass('a-service');
        /** @var array<array-key, Tag> $processTags */
        $processTags = $process->tags;
        $keys = array_map(static fn(Tag $tag): string => (string) $tag->key, $processTags);

        foreach ([
            'jaeger.version',
            'jaeger.hostname',
            'php.bin',
            'php.version',
            'process.pid',
            'process.sapi',
            'process.uid',
            'process.gid',
        ] as $expected) {
            self::assertContains($expected, $keys);
        }
    }

    /**
     * @param class-string<AbstractProcess> $processClass
     */
    #[DataProvider('processCases')]
    public function testShouldReportTheProcessIp(string $processClass): void
    {
        $process = new $processClass('a-service');
        /** @var array<array-key, Tag> $processTags */
        $processTags = $process->tags;
        $keys = array_map(static fn(Tag $tag): string => (string) $tag->key, $processTags);

        self::assertContains('ip', $keys);
    }

    /**
     * @return iterable<string, array{class-string<AbstractProcess>}>
     */
    public static function processCases(): iterable
    {
        yield '✅ CliProcess' => [CliProcess::class];
        yield '✅ FpmProcess' => [FpmProcess::class];
        yield '✅ InternalServerProcess' => [InternalServerProcess::class];
    }

    public function testShouldKeepCallerSuppliedTagsAlongsideTheRuntimeTags(): void
    {
        $process = new CliProcess('a-service');
        /** @var array<array-key, Tag> $processTags */
        $processTags = $process->tags;
        $keys = array_map(static fn(Tag $tag): string => (string) $tag->key, $processTags);

        self::assertSame('ip', $keys[0], 'caller tags come first');
        self::assertGreaterThan(8, \count($keys));
    }

    public function testShouldReportTheHostnameItIsRunningOn(): void
    {
        $tag = new JaegerHostnameTag();

        self::assertSame('jaeger.hostname', $tag->key);
        self::assertSame(gethostname(), $tag->vStr);
    }

    public function testShouldReportANonEmptyIp(): void
    {
        $tag = new ProcessIpTag();

        self::assertSame('ip', $tag->key);
        self::assertSame(TagType::STRING, $tag->vType);
        self::assertNotSame('', $tag->vStr);
    }

    public function testShouldPreferTheServerAddressForTheIpWhenAvailable(): void
    {
        $original = $_SERVER['SERVER_ADDR'] ?? null;
        $_SERVER['SERVER_ADDR'] = '10.11.12.13';

        try {
            self::assertSame('10.11.12.13', new ProcessIpTag()->vStr);
        } finally {
            if (null === $original) {
                unset($_SERVER['SERVER_ADDR']);
            } else {
                $_SERVER['SERVER_ADDR'] = $original;
            }
        }
    }

    public function testShouldFallBackToTheHostnameWhenServerAddressIsEmpty(): void
    {
        $original = $_SERVER['SERVER_ADDR'] ?? null;
        $_SERVER['SERVER_ADDR'] = '';

        try {
            self::assertNotSame('', new ProcessIpTag()->vStr);
        } finally {
            if (null === $original) {
                unset($_SERVER['SERVER_ADDR']);
            } else {
                $_SERVER['SERVER_ADDR'] = $original;
            }
        }
    }

    public function testShouldReportTheRunningProcessIdentity(): void
    {
        self::assertSame(getmypid(), new ProcessPidTag()->vLong);
        self::assertSame(getmyuid(), new ProcessUidTag()->vLong);
        self::assertSame(getmygid(), new ProcessGidTag()->vLong);
    }

    public function testShouldAcceptAnEmptyExtraTagList(): void
    {
        $process = new class ('a-service') extends AbstractProcess {};

        self::assertSame('a-service', $process->serviceName);
        self::assertCount(8, $process->tags ?? [], 'only the runtime tags are present');
    }

    public function testShouldPlaceCallerTagsBeforeRuntimeTags(): void
    {
        $process = new class ('a-service', [new ComponentTag('db')]) extends AbstractProcess {};

        self::assertSame('component', ($process->tags ?? [])[0]->key);
    }
}
