<?php

declare(strict_types=1);

namespace MiGears\Log;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;

/**
 * Minimalist PSR-3 compliant logger.
 *
 * Single class, no handler chain — the handler is a callable.
 * Supports log level thresholds, channel names, and PSR-3
 * context interpolation ({placeholder} syntax).
 *
 * Usage:
 *   $log = Logger::toFile('/tmp/app.log', LogLevel::DEBUG, 'app');
 *   $log->info('User {user} logged in', ['user' => 'Alice']);
 *
 *   // Or use a custom handler
 *   $log = new Logger(fn(string $line) => file_put_contents('php://stderr', $line));
 */
class Logger extends AbstractLogger
{
    public const VERSION = '2.0.0';

    /** @var array<string, int> Log level priority (higher = more severe) */
    private const LEVELS = [
        LogLevel::DEBUG     => 0,
        LogLevel::INFO      => 1,
        LogLevel::NOTICE    => 2,
        LogLevel::WARNING   => 3,
        LogLevel::ERROR     => 4,
        LogLevel::CRITICAL  => 5,
        LogLevel::ALERT     => 6,
        LogLevel::EMERGENCY => 7,
    ];

    /** @var callable(string): void */
    private readonly mixed $handler;

    private readonly int $minLevel;

    /**
     * @param callable(string): void $handler  Function that receives the formatted log line
     * @param string $minLevel                 Minimum log level (below this are discarded)
     * @param string $channel                  Optional channel name prepended to each line
     */
    public function __construct(
        callable $handler,
        string $minLevel = LogLevel::DEBUG,
        private readonly string $channel = '',
    ) {
        $this->handler = $handler;

        // Levels are defined in lower case; accept any case rather than letting
        // a differently cased level silently fall through to the debug default.
        $normalized = strtolower($minLevel);
        if (!isset(self::LEVELS[$normalized])) {
            throw new \InvalidArgumentException(sprintf('Unknown log level "%s".', $minLevel));
        }
        $this->minLevel = self::LEVELS[$normalized];
    }

    /**
     * Create a logger that writes to a file.
     *
     * @param string $path    Path to the log file
     * @param string $level   Minimum log level
     * @param string $channel Optional channel name
     */
    public static function toFile(
        string $path,
        string $level = LogLevel::DEBUG,
        string $channel = '',
    ): self {
        $handler = function (string $line) use ($path): void {
            // A discarded return value loses the line silently on a full disk or
            // a missing directory; surface it as an error instead. The @ keeps
            // the native warning from duplicating the exception below.
            if (@file_put_contents($path, $line . \PHP_EOL, FILE_APPEND | LOCK_EX) === false) {
                throw new \RuntimeException(sprintf('Unable to write a log line to "%s".', $path));
            }
        };

        return new self($handler, $level, $channel);
    }

    /**
     * Create a logger that writes to a stream resource.
     *
     * @param resource $stream  Stream resource (e.g. STDERR, fopen('php://stdout', 'w'))
     * @param string   $level   Minimum log level
     * @param string   $channel Optional channel name
     */
    public static function toStream(
        $stream,
        string $level = LogLevel::DEBUG,
        string $channel = '',
    ): self {
        if (!is_resource($stream)) {
            throw new \InvalidArgumentException('Logger::toStream() expects an open stream resource.');
        }

        $handler = function (string $line) use ($stream): void {
            // Same reasoning as toFile(): a failed write must not be silent.
            if (@fwrite($stream, $line . \PHP_EOL) === false) {
                throw new \RuntimeException('Unable to write a log line to the stream.');
            }
        };

        return new self($handler, $level, $channel);
    }

    /**
     * Create a null logger that discards all messages.
     *
     * Useful for testing or disabling logging in production.
     */
    public static function null(): self
    {
        return new self(fn() => null, LogLevel::EMERGENCY);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed             $level   PSR-3 log level string
     * @param string|Stringable $message Log message
     * @param array<string, mixed> $context Context array for interpolation
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $level = (string) $level;
        // PSR-3 levels are lower case; normalise so "INFO" is not read as an
        // unknown level and demoted to debug.
        $priority = self::LEVELS[strtolower($level)] ?? 0;

        if ($priority < $this->minLevel) {
            return;
        }

        $interpolated = $this->interpolate((string) $message, $context);
        $line = $this->format($level, $interpolated);

        ($this->handler)($line);
    }

    /**
     * Interpolate {placeholders} in the message with context values.
     *
     * Per PSR-3 spec: placeholders are surrounded by braces, and context
     * keys match the placeholder names (without the braces).
     *
     * @param array<string, mixed> $context
     */
    private function interpolate(string $message, array $context): string
    {
        if ($context === []) {
            return $message;
        }

        $replace = [];
        foreach ($context as $key => $value) {
            if (is_bool($value)) {
                // (string) false is '', which reads as "no value at all"; spell
                // booleans out so a false flag is visible in the line.
                $replace['{' . $key . '}'] = $value ? 'true' : 'false';
            } elseif (is_scalar($value) || $value instanceof Stringable) {
                $replace['{' . $key . '}'] = (string) $value;
            } elseif ($value === null) {
                $replace['{' . $key . '}'] = 'null';
            }
        }

        return strtr($message, $replace);
    }

    /**
     * Format a log line.
     *
     * Format: [YYYY-MM-DD HH:MM:SS] [channel] LEVEL: message
     */
    private function format(string $level, string $message): string
    {
        $timestamp = date('Y-m-d H:i:s');
        $level = strtoupper($level);

        $prefix = "[{$timestamp}]";
        if ($this->channel !== '') {
            $prefix .= " [{$this->channel}]";
        }
        $prefix .= " {$level}:";

        return "{$prefix} {$message}";
    }
}
