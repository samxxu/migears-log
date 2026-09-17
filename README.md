# migears/log

![Version](https://img.shields.io/badge/version-2.0.0-blue)

Minimalist PSR-3 compliant logger for PHP. One class, zero magic.

A single `Logger` class that implements the full PSR-3 interface. No handler chains, no formatters, no configuration files. Just a callable handler, level threshold, and channel support. ~150 lines total.

## Features

- **Full PSR-3 compliance** — all 8 log levels + `log()` method
- **Context interpolation** — `{placeholder}` syntax per PSR-3 spec
- **Level threshold** — discard messages below a minimum level
- **Channel support** — tag log lines with a channel name
- **Three built-in handlers** — file, stream, null
- **Custom handlers** — pass any callable
- **Single class, ~150 lines** — read and understand the whole thing in minutes
- **Only dependency: psr/log** — the standard interface

## Installation

```bash
composer require migears/log
```

Requires: PHP 8.1+.

## Quick Start

```php
use MiGears\Log\Logger;
use Psr\Log\LogLevel;

// File logger
$log = Logger::toFile('/var/log/app.log', LogLevel::DEBUG, 'app');

$log->info('User {user} logged in', ['user' => 'Alice']);
$log->warning('Disk space low: {percent}%', ['percent' => 85]);
$log->error('Database connection failed', ['host' => 'db1']);
```

### Stream Logger (stderr)

```php
$log = Logger::toStream(STDERR, LogLevel::WARNING, 'cli');
$log->error('Something went wrong');
```

### Null Logger (testing / production disable)

```php
$log = Logger::null();
$log->debug('This will be silently discarded');
```

### Custom Handler

```php
// Send logs to any destination via a callable
$log = new Logger(function (string $line): void {
    // e.g. send to syslog, Slack, database...
    syslog(LOG_INFO, $line);
}, LogLevel::INFO, 'app');
```

### Log Levels

```php
use Psr\Log\LogLevel;

// From lowest to highest priority:
LogLevel::DEBUG      // 0
LogLevel::INFO       // 1
LogLevel::NOTICE     // 2
LogLevel::WARNING    // 3
LogLevel::ERROR      // 4
LogLevel::CRITICAL   // 5
LogLevel::ALERT      // 6
LogLevel::EMERGENCY  // 7
```

### Context Interpolation

Per PSR-3 spec, `{placeholder}` in the message is replaced with the corresponding context value:

```php
$log->info('Hello, {name}! You have {count} messages.', [
    'name' => 'Bob',
    'count' => 5,
]);
// Output: [2024-01-01 12:00:00] [app] INFO: Hello, Bob! You have 5 messages.
```

Scalar values and `Stringable` objects are interpolated. `null` becomes `"null"`. Non-scalar values (arrays, objects) are left as-is (placeholder stays).

### Output Format

```
[YYYY-MM-DD HH:MM:SS] [channel] LEVEL: message
```

Example:
```
[2024-01-15 10:30:45] [app] INFO: User Alice logged in
[2024-01-15 10:31:02] [app] WARNING: Rate limit approaching for user 123
[2024-01-15 10:31:10] [db] ERROR: Connection timeout on host db2
```

## API Reference

| Method | Description |
|--------|-------------|
| `new Logger(callable $handler, string $minLevel = DEBUG, string $channel = '')` | Create with custom handler |
| `Logger::toFile(string $path, $level = DEBUG, $channel = '')` | Create file logger |
| `Logger::toStream($stream, $level = DEBUG, $channel = '')` | Create stream logger |
| `Logger::null()` | Create null (no-op) logger |
| `debug($message, $context = [])` | Debug level log |
| `info($message, $context = [])` | Info level log |
| `notice($message, $context = [])` | Notice level log |
| `warning($message, $context = [])` | Warning level log |
| `error($message, $context = [])` | Error level log |
| `critical($message, $context = [])` | Critical level log |
| `alert($message, $context = [])` | Alert level log |
| `emergency($message, $context = [])` | Emergency level log |
| `log($level, $message, $context = [])` | Log at arbitrary level |

## Design Philosophy

miGears Log follows the miGears philosophy: **minimal, readable, and useful**.

- **One class** — no handler chains, no formatters, no processors
- **Callable handler** — flexibility without interface bloat
- **PSR-3 compliant** — drop-in replacement for any PSR-3 logger
- **Small enough to read** — ~150 lines of code

**What we don't do**:
- No handler stacks / middleware chains
- No log rotation (use logrotate or similar tools)
- No structured / JSON logging out of the box (format it in your handler)
- No built-in async / buffered logging (implement in your handler)
- No configuration files or DI container integration

## Integration with miGears Web

```php
use MiGears\Web\MiRest;
use MiGears\Log\Logger;

$rest = new MiRest(__DIR__ . '/resources', 'App\\Resources');

// Register logger as a service
$rest->set('logger', function () {
    return Logger::toFile(__DIR__ . '/logs/app.log', 'debug', 'app');
});

// In a resource:
class Users extends AbstractResource
{
    public function POST(Request $request): Response
    {
        $this->service('logger')->info('User created', ['id' => $userId]);
        return Response::json(['status' => 'ok']);
    }
}
```

## License

MIT

---

# migears/log

![Version](https://img.shields.io/badge/version-2.0.0-blue)

极简 PSR-3 兼容 PHP 日志器。一个类，零魔法。

单个 `Logger` 类实现完整的 PSR-3 接口。没有处理器链，没有格式化器，没有配置文件。只有一个可调用的 handler、级别阈值和 channel 支持。总共约 150 行代码。

## 特性

- **完全 PSR-3 兼容** — 全部 8 个日志级别 + `log()` 方法
- **上下文插值** — 按 PSR-3 规范的 `{placeholder}` 语法
- **级别阈值** — 丢弃低于最小级别的消息
- **Channel 支持** — 给日志行打上 channel 名称标签
- **三种内置 handler** — 文件、流、空操作
- **自定义 handler** — 传入任意可调用函数
- **单类约 150 行** — 几分钟就能读完理解
- **唯一依赖：psr/log** — 标准接口

## 安装

```bash
composer require migears/log
```

要求：PHP 8.1+。

## 快速开始

```php
use MiGears\Log\Logger;
use Psr\Log\LogLevel;

// 文件日志
$log = Logger::toFile('/var/log/app.log', LogLevel::DEBUG, 'app');

$log->info('用户 {user} 已登录', ['user' => 'Alice']);
$log->warning('磁盘空间不足：{percent}%', ['percent' => 85]);
$log->error('数据库连接失败', ['host' => 'db1']);
```

### 流日志（stderr）

```php
$log = Logger::toStream(STDERR, LogLevel::WARNING, 'cli');
$log->error('出错了');
```

### 空日志器（测试 / 生产环境禁用）

```php
$log = Logger::null();
$log->debug('这条会被静默丢弃');
```

### 自定义 Handler

```php
// 通过可调用函数将日志发送到任何目的地
$log = new Logger(function (string $line): void {
    // 例如发送到 syslog、Slack、数据库...
    syslog(LOG_INFO, $line);
}, LogLevel::INFO, 'app');
```

### 日志级别

```php
use Psr\Log\LogLevel;

// 从低到高优先级：
LogLevel::DEBUG      // 0
LogLevel::INFO       // 1
LogLevel::NOTICE     // 2
LogLevel::WARNING    // 3
LogLevel::ERROR      // 4
LogLevel::CRITICAL   // 5
LogLevel::ALERT      // 6
LogLevel::EMERGENCY  // 7
```

### 上下文插值

按 PSR-3 规范，消息中的 `{placeholder}` 会被对应的上下文值替换：

```php
$log->info('你好，{name}！你有 {count} 条消息。', [
    'name' => 'Bob',
    'count' => 5,
]);
// 输出：[2024-01-01 12:00:00] [app] INFO: 你好，Bob！你有 5 条消息。
```

标量值和 `Stringable` 对象会被插值。`null` 变为 `"null"`。非标量值（数组、对象）保持原样（占位符保留）。

### 输出格式

```
[YYYY-MM-DD HH:MM:SS] [channel] LEVEL: message
```

示例：
```
[2024-01-15 10:30:45] [app] INFO: 用户 Alice 已登录
[2024-01-15 10:31:02] [app] WARNING: 用户 123 即将达到速率限制
[2024-01-15 10:31:10] [db] ERROR: 主机 db2 连接超时
```

## API 参考

| 方法 | 说明 |
|------|------|
| `new Logger(callable $handler, string $minLevel = DEBUG, string $channel = '')` | 使用自定义 handler 创建 |
| `Logger::toFile(string $path, $level = DEBUG, $channel = '')` | 创建文件日志器 |
| `Logger::toStream($stream, $level = DEBUG, $channel = '')` | 创建流日志器 |
| `Logger::null()` | 创建空（无操作）日志器 |
| `debug($message, $context = [])` | Debug 级别日志 |
| `info($message, $context = [])` | Info 级别日志 |
| `notice($message, $context = [])` | Notice 级别日志 |
| `warning($message, $context = [])` | Warning 级别日志 |
| `error($message, $context = [])` | Error 级别日志 |
| `critical($message, $context = [])` | Critical 级别日志 |
| `alert($message, $context = [])` | Alert 级别日志 |
| `emergency($message, $context = [])` | Emergency 级别日志 |
| `log($level, $message, $context = [])` | 任意级别日志 |

## 设计哲学

miGears Log 遵循 miGears 设计哲学：**极简、可读、实用**。

- **一个类** — 没有 handler 链、没有格式化器、没有处理器
- **可调用 handler** — 灵活但不臃肿
- **PSR-3 兼容** — 可直接替换任何 PSR-3 日志器
- **小到可以读完** — 约 150 行代码

**我们不做的事**：
- 没有 handler 栈 / 中间件链
- 没有日志轮转（使用 logrotate 或类似工具）
- 没有内置结构化 / JSON 日志（在 handler 中自己格式化）
- 没有内置异步 / 缓冲日志（在 handler 中实现）
- 没有配置文件或 DI 容器集成

## 与 miGears Web 集成

```php
use MiGears\Web\MiRest;
use MiGears\Log\Logger;

$rest = new MiRest(__DIR__ . '/resources', 'App\\Resources');

// 将日志器注册为服务
$rest->set('logger', function () {
    return Logger::toFile(__DIR__ . '/logs/app.log', 'debug', 'app');
});

// 在资源类中：
class Users extends AbstractResource
{
    public function POST(Request $request): Response
    {
        $this->service('logger')->info('用户已创建', ['id' => $userId]);
        return Response::json(['status' => 'ok']);
    }
}
```

## 许可证

MIT
