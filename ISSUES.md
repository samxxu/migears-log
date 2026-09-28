# migears-log — Known Issues

> Summary of this module's issues. The items themselves are in [`issues/`](issues/README.md), one file
> per item: a front-matter header and a thread. This file is generated from them and can be rewritten at
> any time; edit an item, never this file.
>
> From the miGears Full-Module Code Review Report (5th round, 2026-09-28).

| | |
|---|---|
| Status | **Best state** |
| Size | src 104 lines (net) · 36 tests · 1 src file |

Legend — **P0** functional or security · **P1** documentation that fails when copied · **P2** robustness · **P3** metadata and docs

## At a glance

| | |
|---|---|
| Unsettled | P0 0 · P1 0 · P2 3 · P3 1 · other 1 |
| Settled | 0 of 5 |
| Waiting on the owner | _nothing_ |
| Waiting on the reviewer | `P2-1`, `P2-2`, `P2-3`, `P3-1`, `G2` |
| Waiting on the coordinator | _nothing_ |
| Deferred, owing nobody | _nothing_ |

| id | level | status | title |
|---|---|---|---|
| [`P2-1`](issues/P2-1.md) | P2 | **fixed** | The constructor declares `$handler` as `mixed` with zero validation; a … |
| [`P2-2`](issues/P2-2.md) | P2 | **fixed** | An unknown or uppercase `$minLevel` disables thresholding: … |
| [`P2-3`](issues/P2-3.md) | P2 | **fixed** | `file_put_contents`/`fwrite` return values are discarded, so a full … |
| [`P3-1`](issues/P3-1.md) | P3 | **fixed** | A boolean `false` context value interpolates to an empty string … |
| [`G2`](issues/G2.md) | - | **fixed** | Strict flags: `phpunit.xml.dist` currently sets none of the five. The … |

## Unclosed

What is left to do here: every item whose `status` is not `verified` or `closed`,
highest severity first. `waiting on` is the party who acts next, read from that status.

| | |
|---|---|
| Unclosed | **5** of 5 |
| By status | `fixed` 5 |
| Waiting on | reviewer 5 |

| level | item | status | waiting on | title |
|---|---|---|---|---|
| **P2** | [`P2-1`](issues/P2-1.md) | `fixed` | reviewer | The constructor declares `$handler` as `mixed` with zero validation; a … |
| **P2** | [`P2-2`](issues/P2-2.md) | `fixed` | reviewer | An unknown or uppercase `$minLevel` disables thresholding: … |
| **P2** | [`P2-3`](issues/P2-3.md) | `fixed` | reviewer | `file_put_contents`/`fwrite` return values are discarded, so a full … |
| **P3** | [`P3-1`](issues/P3-1.md) | `fixed` | reviewer | A boolean `false` context value interpolates to an empty string … |
| **-** | [`G2`](issues/G2.md) | `fixed` | reviewer | Strict flags: `phpunit.xml.dist` currently sets none of the five. The … |

## Verdict

A minimalist PSR-3 logger in a single ~100-line class — exactly what it claims to be, no handler chains, no formatters, everything tested.

## Fixed since the last round

All four prior items confirmed fixed: handler typed callable, unknown/uppercase level throws, write failures throw, boolean interpolation works correctly; G2 strict flags complete.

## Test gaps

No test for partial write (fwrite returns less than length) on the stream path; no test for concurrent file writes / lock contention semantics.

## Verification protocol

- `./vendor/bin/phpunit` · `composer analyse` · `composer validate`
- Warning/notice/deprecation/risky flags in `phpunit.xml.dist`: all four on
- A PHP warning counts as a test failure only where those flags are on; otherwise run `./vendor/bin/phpunit --fail-on-warning` explicitly.


---

# migears-log — 已知问题

> 本模块问题的概览。条目本体在 [`issues/`](issues/README.md)，一条目一文件：前置字段加讨论串。
> 本文件由条目生成，随时可以整段重写；请改条目，不要改本文件。
>
> 出自 miGears 全模块代码评审报告（5th round，2026-09-28）。

| | |
|---|---|
| 状态 | **状态最好** |
| 体量 | src 104 行（净）· 36 个用例 · 1 个源文件 |

级别说明 — **P0** 功能性或安全级 · **P1** 文档照抄即错 · **P2** 健壮性 · **P3** 元数据与文档

## 状态一览

| | |
|---|---|
| 未了结 | P0 0 · P1 0 · P2 3 · P3 1 · 其他 1 |
| 已了结 | 0 / 5 |
| 等负责人 | _无_ |
| 等评审方 | `P2-1`, `P2-2`, `P2-3`, `P3-1`, `G2` |
| 等协调人 | _无_ |
| 已暂缓，不欠谁 | _无_ |

| id | 级别 | 状态 | 标题 |
|---|---|---|---|
| [`P2-1`](issues/P2-1.md) | P2 | **fixed** | 构造器把 $handler 声明为 mixed 且零校验；非 callable 只在调用时以 Error: Call to undefined … |
| [`P2-2`](issues/P2-2.md) | P2 | **fixed** | 未知或大写的 $minLevel 会让阈值失效：LEVELS[$minLevel] ?? 0 使 new Logger($h, "INFO") … |
| [`P2-3`](issues/P2-3.md) | P2 | **fixed** | file_put_contents/fwrite 的返回值被丢弃，磁盘满、权限不足或目录不存在都会静默丢日志；toStream() … |
| [`P3-1`](issues/P3-1.md) | P3 | **fixed** | 布尔 false 的上下文值插值成空串（info("flag={flag}", ["flag" => false]) 输出 flag=），只有 … |
| [`G2`](issues/G2.md) | - | **fixed** | 严格开关：`phpunit.xml.dist` … |

## 未关闭

本模块还剩什么要做：所有 `status` 不是 `verified` 或 `closed` 的条目，按严重度从高到低。
`waiting on` 是下一步该动手的一方，由其状态读出。

| | |
|---|---|
| 未关闭 | **5** / 5 |
| 按状态 | `fixed` 5 |
| 等在谁 | 评审方 5 |

| 级别 | 条目 | 状态 | 等在谁 | 标题 |
|---|---|---|---|---|
| **P2** | [`P2-1`](issues/P2-1.md) | `fixed` | 评审方 | 构造器把 $handler 声明为 mixed 且零校验；非 callable 只在调用时以 Error: Call to undefined … |
| **P2** | [`P2-2`](issues/P2-2.md) | `fixed` | 评审方 | 未知或大写的 $minLevel 会让阈值失效：LEVELS[$minLevel] ?? 0 使 new Logger($h, "INFO") … |
| **P2** | [`P2-3`](issues/P2-3.md) | `fixed` | 评审方 | file_put_contents/fwrite 的返回值被丢弃，磁盘满、权限不足或目录不存在都会静默丢日志；toStream() … |
| **P3** | [`P3-1`](issues/P3-1.md) | `fixed` | 评审方 | 布尔 false 的上下文值插值成空串（info("flag={flag}", ["flag" => false]) 输出 flag=），只有 … |
| **-** | [`G2`](issues/G2.md) | `fixed` | 评审方 | 严格开关：`phpunit.xml.dist` … |

## 结论

单文件约 100 行的极简 PSR-3 日志器——名副其实，无处理器链、无格式化器、全覆盖测试。

## 本轮已修复确认

All four prior items confirmed fixed: handler typed callable, unknown/uppercase level throws, write failures throw, boolean interpolation works correctly; G2 strict flags complete.

## 测试盲区

无流路径部分写入（fwrite 返回小于总长度）测试；无并发文件写入/锁竞争语义测试。

## 验证方式

- `./vendor/bin/phpunit` · `composer analyse` · `composer validate`
- `phpunit.xml.dist` 中的 warning/notice/deprecation/risky 开关：四个全开
- 只有在上述开关打开时 PHP 警告才会导致套件失败；否则请显式加 `--fail-on-warning`。
