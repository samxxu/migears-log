# migears-log — Known Issues

> Summary of this module's issues. The items themselves are in [`issues/`](issues/README.md), one file
> per item: a front-matter header and a thread. This file is generated from them and can be rewritten at
> any time; edit an item, never this file.
>
> From the miGears Full-Module Code Review Report (6th round, 2026-10-01).

| | |
|---|---|
| Status | **P2 open** |
| Size | src 104 lines (net) · 36 tests · 1 src file |

Legend — **P0** functional or security · **P1** documentation that fails when copied · **P2** robustness · **P3** metadata and docs

## At a glance

| | |
|---|---|
| Unsettled | P0 0 · P1 0 · P2 1 · P3 1 · other 0 |
| Settled | 5 of 7 |
| Waiting on the owner | _nothing_ |
| Waiting on the coordinator | _nothing_ |
| Waiting on the reviewer | `P2-4`, `P3-2` |
| Deferred, owing nobody | _nothing_ |

| id | level | status | title |
|---|---|---|---|
| [`P2-1`](issues/P2-1.md) | P2 | **verified** | The constructor declares `$handler` as `mixed` with zero validation; a … |
| [`P2-2`](issues/P2-2.md) | P2 | **verified** | An unknown or uppercase `$minLevel` disables thresholding: … |
| [`P2-3`](issues/P2-3.md) | P2 | **verified** | `file_put_contents`/`fwrite` return values are discarded, so a full … |
| [`P2-4`](issues/P2-4.md) | P2 | **fixed** | log() maps an undefined level to debug priority with '?? 0' and emits … |
| [`P3-1`](issues/P3-1.md) | P3 | **verified** | A boolean `false` context value interpolates to an empty string … |
| [`P3-2`](issues/P3-2.md) | P3 | **rejected** | Logger::null() uses LogLevel::EMERGENCY as minLevel, meaning … |
| [`G2`](issues/G2.md) | - | **verified** | Strict flags: `phpunit.xml.dist` currently sets none of the five. The … |

## Unclosed

What is left to do here: every item whose `status` is not `verified` or `closed`,
highest severity first. `waiting on` is the party who acts next, read from that status.

| | |
|---|---|
| Unclosed | **2** of 7 |
| By status | `rejected` 1 · `fixed` 1 |
| Waiting on | reviewer 2 |

| level | item | status | waiting on | title |
|---|---|---|---|---|
| **P2** | [`P2-4`](issues/P2-4.md) | `fixed` | reviewer | log() maps an undefined level to debug priority with '?? 0' and emits … |
| **P3** | [`P3-2`](issues/P3-2.md) | `rejected` | reviewer | Logger::null() uses LogLevel::EMERGENCY as minLevel, meaning … |

## Verdict

A clean, honest single-class PSR-3 logger — except that one of its two level paths does not do what PSR-3 requires, while the README claims full compliance.

## Fixed since the last round

No item was awaiting a verdict; the four earlier fixes hold (handler typed callable, unknown level throws at construction, write failures throw, booleans interpolate as true/false) and the P3-2 rejection stands.

## Test gaps

No test asserts that an unknown level throws, and the existing testInvalidLevelTreatedAsDebug pins the opposite; a partial fwrite (a positive count below the requested length) is not detected; LOCK_EX contention is unasserted.

## Verification protocol

- `./vendor/bin/phpunit` · `composer analyse` · `composer validate`
- Warning/notice/deprecation/risky flags in `phpunit.xml.dist`: all four on
- A PHP warning counts as a test failure only where those flags are on; otherwise run `./vendor/bin/phpunit --fail-on-warning` explicitly.


---

# migears-log — 已知问题

> 本模块问题的概览。条目本体在 [`issues/`](issues/README.md)，一条目一文件：前置字段加讨论串。
> 本文件由条目生成，随时可以整段重写；请改条目，不要改本文件。
>
> 出自 miGears 全模块代码评审报告（6th round，2026-10-01）。

| | |
|---|---|
| 状态 | **P2 待修** |
| 体量 | src 104 行（净）· 36 个用例 · 1 个源文件 |

级别说明 — **P0** 功能性或安全级 · **P1** 文档照抄即错 · **P2** 健壮性 · **P3** 元数据与文档

## 状态一览

| | |
|---|---|
| 未了结 | P0 0 · P1 0 · P2 1 · P3 1 · 其他 0 |
| 已了结 | 5 / 7 |
| 等模块主 | _无_ |
| 等协调人 | _无_ |
| 等评审方 | `P2-4`, `P3-2` |
| 已暂缓，不欠谁 | _无_ |

| id | 级别 | 状态 | 标题 |
|---|---|---|---|
| [`P2-1`](issues/P2-1.md) | P2 | **verified** | 构造器把 $handler 声明为 mixed 且零校验；非 callable 只在调用时以 Error: Call to undefined … |
| [`P2-2`](issues/P2-2.md) | P2 | **verified** | 未知或大写的 $minLevel 会让阈值失效：LEVELS[$minLevel] ?? 0 使 new Logger($h, "INFO") … |
| [`P2-3`](issues/P2-3.md) | P2 | **verified** | file_put_contents/fwrite 的返回值被丢弃，磁盘满、权限不足或目录不存在都会静默丢日志；toStream() … |
| [`P2-4`](issues/P2-4.md) | P2 | **fixed** | log() 用「?? 0」把未定义的级别映射为 debug 优先级后照常输出，而不是按 PSR-3 要求（也是所装接口文档声明的）抛 … |
| [`P3-1`](issues/P3-1.md) | P3 | **verified** | 布尔 false 的上下文值插值成空串（info("flag={flag}", ["flag" => false]) 输出 flag=），只有 … |
| [`P3-2`](issues/P3-2.md) | P3 | **rejected** | Logger::null() 使用 LogLevel::EMERGENCY 作为 minLevel，意味着 emergency() … |
| [`G2`](issues/G2.md) | - | **verified** | 严格开关：`phpunit.xml.dist` … |

## 未关闭

本模块还剩什么要做：所有 `status` 不是 `verified` 或 `closed` 的条目，按严重度从高到低。
`waiting on` 是下一步该动手的一方，由其状态读出。

| | |
|---|---|
| 未关闭 | **2** / 7 |
| 按状态 | `rejected` 1 · `fixed` 1 |
| 等在谁 | 评审方 2 |

| 级别 | 条目 | 状态 | 等在谁 | 标题 |
|---|---|---|---|---|
| **P2** | [`P2-4`](issues/P2-4.md) | `fixed` | 评审方 | log() 用「?? 0」把未定义的级别映射为 debug 优先级后照常输出，而不是按 PSR-3 要求（也是所装接口文档声明的）抛 … |
| **P3** | [`P3-2`](issues/P3-2.md) | `rejected` | 评审方 | Logger::null() 使用 LogLevel::EMERGENCY 作为 minLevel，意味着 emergency() … |

## 结论

一个干净、诚实的单类 PSR-3 日志器——只是它的两条级别路径中有一条不满足 PSR-3，而 README 声称完全兼容。

## 本轮已修复确认

No item was awaiting a verdict; the four earlier fixes hold (handler typed callable, unknown level throws at construction, write failures throw, booleans interpolate as true/false) and the P3-2 rejection stands.

## 测试盲区

无「未知级别应抛异常」的用例，而现有 testInvalidLevelTreatedAsDebug 恰好钉住了相反行为；部分写（返回值小于请求长度）未被检测；LOCK_EX 竞争无断言。

## 验证方式

- `./vendor/bin/phpunit` · `composer analyse` · `composer validate`
- `phpunit.xml.dist` 中的 warning/notice/deprecation/risky 开关：四个全开
- 只有在上述开关打开时 PHP 警告才会导致套件失败；否则请显式加 `--fail-on-warning`。
