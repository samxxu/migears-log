<?php

declare(strict_types=1);

namespace MiGears\Log\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use MiGears\Log\Logger;
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;

#[CoversClass(Logger::class)]
final class LoggerTest extends TestCase
{
    /** @var list<string> Captured log lines */
    private array $lines = [];

    private function createLogger(string $level = LogLevel::DEBUG, string $channel = ''): Logger
    {
        $this->lines = [];
        $handler = function (string $line): void {
            $this->lines[] = $line;
        };

        return new Logger($handler, $level, $channel);
    }

    // --- PSR-3 Interface ---

    public function testImplementsLoggerInterface(): void
    {
        $log = $this->createLogger();
        self::assertInstanceOf(LoggerInterface::class, $log);
    }

    // --- All 8 log level methods ---

    public function testDebug(): void
    {
        $log = $this->createLogger();
        $log->debug('debug message');
        self::assertCount(1, $this->lines);
        self::assertStringContainsString('DEBUG:', $this->lines[0]);
        self::assertStringContainsString('debug message', $this->lines[0]);
    }

    public function testInfo(): void
    {
        $log = $this->createLogger();
        $log->info('info message');
        self::assertStringContainsString('INFO:', $this->lines[0]);
    }

    public function testNotice(): void
    {
        $log = $this->createLogger();
        $log->notice('notice message');
        self::assertStringContainsString('NOTICE:', $this->lines[0]);
    }

    public function testWarning(): void
    {
        $log = $this->createLogger();
        $log->warning('warning message');
        self::assertStringContainsString('WARNING:', $this->lines[0]);
    }

    public function testError(): void
    {
        $log = $this->createLogger();
        $log->error('error message');
        self::assertStringContainsString('ERROR:', $this->lines[0]);
    }

    public function testCritical(): void
    {
        $log = $this->createLogger();
        $log->critical('critical message');
        self::assertStringContainsString('CRITICAL:', $this->lines[0]);
    }

    public function testAlert(): void
    {
        $log = $this->createLogger();
        $log->alert('alert message');
        self::assertStringContainsString('ALERT:', $this->lines[0]);
    }

    public function testEmergency(): void
    {
        $log = $this->createLogger();
        $log->emergency('emergency message');
        self::assertStringContainsString('EMERGENCY:', $this->lines[0]);
    }

    // --- log() method ---

    public function testLogWithArbitraryLevel(): void
    {
        $log = $this->createLogger();
        $log->log(LogLevel::WARNING, 'test warning');
        self::assertStringContainsString('WARNING:', $this->lines[0]);
        self::assertStringContainsString('test warning', $this->lines[0]);
    }

    // --- Context interpolation ---

    public function testContextInterpolation(): void
    {
        $log = $this->createLogger();
        $log->info('User {user} logged in from {ip}', [
            'user' => 'Alice',
            'ip' => '127.0.0.1',
        ]);

        self::assertStringContainsString('User Alice logged in from 127.0.0.1', $this->lines[0]);
    }

    public function testContextInterpolationWithNull(): void
    {
        $log = $this->createLogger();
        $log->info('Value is {val}', ['val' => null]);
        self::assertStringContainsString('Value is null', $this->lines[0]);
    }

    public function testContextInterpolationWithNumeric(): void
    {
        $log = $this->createLogger();
        $log->info('Count: {count}', ['count' => 42]);
        self::assertStringContainsString('Count: 42', $this->lines[0]);
    }

    public function testContextWithNonScalarIsIgnored(): void
    {
        $log = $this->createLogger();
        $log->info('Data: {data}', ['data' => ['nested' => 'value']]);
        // Non-scalar values are not interpolated (placeholder stays)
        self::assertStringContainsString('{data}', $this->lines[0]);
    }

    public function testEmptyContext(): void
    {
        $log = $this->createLogger();
        $log->info('simple message', []);
        self::assertStringContainsString('simple message', $this->lines[0]);
    }

    // --- Level threshold ---

    public function testBelowThresholdIsDiscarded(): void
    {
        $log = $this->createLogger(LogLevel::WARNING);
        $log->debug('should not appear');
        $log->info('should not appear');
        $log->notice('should not appear');

        self::assertCount(0, $this->lines);
    }

    public function testAtAndAboveThresholdPasses(): void
    {
        $log = $this->createLogger(LogLevel::WARNING);
        $log->warning('warn');
        $log->error('error');
        $log->critical('critical');
        $log->alert('alert');
        $log->emergency('emergency');

        self::assertCount(5, $this->lines);
    }

    public function testInvalidLevelTreatedAsDebug(): void
    {
        $log = $this->createLogger(LogLevel::INFO);
        $log->log('unknown_level', 'test');
        // Unknown level has priority 0 (same as debug), below INFO threshold
        self::assertCount(0, $this->lines);
    }

    // --- Channel ---

    public function testChannelNameInOutput(): void
    {
        $log = $this->createLogger(LogLevel::DEBUG, 'app');
        $log->info('hello');

        self::assertStringContainsString('[app]', $this->lines[0]);
    }

    public function testNoChannelWhenEmpty(): void
    {
        $log = $this->createLogger();
        $log->info('hello');

        self::assertStringNotContainsString('[]', $this->lines[0]);
    }

    // --- Timestamp ---

    public function testTimestampInOutput(): void
    {
        $log = $this->createLogger();
        $log->info('test');

        // Format: [YYYY-MM-DD HH:MM:SS]
        self::assertMatchesRegularExpression('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $this->lines[0]);
    }

    // --- Static factories ---

    public function testToFile(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'migears_log_test_');

        try {
            $log = Logger::toFile($path, LogLevel::DEBUG, 'test');
            $log->info('file log test');

            $contents = file_get_contents($path);
            self::assertStringContainsString('INFO:', $contents);
            self::assertStringContainsString('file log test', $contents);
            self::assertStringContainsString('[test]', $contents);
        } finally {
            @unlink($path);
        }
    }

    public function testToStream(): void
    {
        $stream = fopen('php://memory', 'r+');
        $log = Logger::toStream($stream, LogLevel::DEBUG, 'stream');
        $log->error('stream log test');

        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);

        self::assertStringContainsString('ERROR:', $contents);
        self::assertStringContainsString('stream log test', $contents);
    }

    public function testNullLogger(): void
    {
        $log = Logger::null();
        $log->emergency('should be discarded');
        $log->alert('should be discarded');

        // No assertion needed — if no exception, it passes
        self::assertInstanceOf(Logger::class, $log);
    }

    // --- Stringable message ---

    public function testStringableMessage(): void
    {
        $msg = new class implements \Stringable {
            public function __toString(): string { return 'stringable message'; }
        };

        $log = $this->createLogger();
        $log->info($msg);
        self::assertStringContainsString('stringable message', $this->lines[0]);
    }

    // --- Version constant ---

    public function testVersionConstant(): void
    {
        self::assertSame('2.0.0', Logger::VERSION);
    }

    // --- Multiple log lines order ---

    public function testLogLinesInOrder(): void
    {
        $log = $this->createLogger();
        $log->debug('first');
        $log->info('second');
        $log->warning('third');

        self::assertCount(3, $this->lines);
        self::assertStringContainsString('DEBUG:', $this->lines[0]);
        self::assertStringContainsString('INFO:', $this->lines[1]);
        self::assertStringContainsString('WARNING:', $this->lines[2]);
    }
}
