# migears-log — Known Issues / 已知问题

> Summary of this module's issues. The items themselves are in [`issues/`](issues/README.md), one file
> per item: a front-matter header and a thread. This file is generated from them and can be rewritten at
> any time; edit an item, never this file.
>
> 本模块问题的概览。条目本体在 [`issues/`](issues/README.md)，一条目一文件：前置字段加讨论串。
> 本文件由条目生成，随时可以整段重写；请改条目，不要改本文件。
>
> From the miGears Full-Module Code Review Report (4th round, 2026-09-27).

| | |
|---|---|
| Status / 状态 | **P0 cleared / P0 已清零** |
| Size / 体量 | src 172 lines (89 net) · 27 tests · 1 src file |

Legend / 图例 — **P0** functional or security · **P1** documentation that fails when copied · **P2** robustness · **P3** metadata and docs
级别说明 — **P0** 功能性或安全级 · **P1** 文档照抄即错 · **P2** 健壮性 · **P3** 元数据与文档

## At a glance / 状态一览

| | |
|---|---|
| Items / 条目 | P0 0 · P1 0 · P2 3 · P3 1 · other 1 |
| Answered / 已回复 | 5 of 5 |
| Waiting / 等待回复 | _nothing / 无_ |

| id | level | status | title |
|---|---|---|---|
| [`P2-1`](issues/P2-1.md) | P2 | **fixed** | The constructor declares `$handler` as `mixed` with zero validation; a … |
| [`P2-2`](issues/P2-2.md) | P2 | **fixed** | An unknown or uppercase `$minLevel` disables thresholding: … |
| [`P2-3`](issues/P2-3.md) | P2 | **fixed** | `file_put_contents`/`fwrite` return values are discarded, so a full … |
| [`P3-1`](issues/P3-1.md) | P3 | **fixed** | A boolean `false` context value interpolates to an empty string … |
| [`G2`](issues/G2.md) | - | **fixed** | Strict flags: `phpunit.xml.dist` currently sets none of the five. The … |

## Verdict / 结论

The smallest module and the only one where nothing moved since the last round. All four previous items are still present, and the two worst are silent: an uppercase `minLevel` disables the threshold entirely, and a boolean `false` in the context prints as empty.

全仓最小，也是本轮唯一「一条都没动」的模块。上一轮四项全部原样存在，其中最糟的两条都是静默的：大写 minLevel 会让阈值彻底失效，布尔 false 在上下文里打印成空。

## Fixed since the last round / 本轮已修复确认

无。上一轮 4 项在本轮全部原样保留——这是本轮唯一一个「一条都没动」的模块。 

## Test gaps / 测试盲区

No uppercase/unknown `minLevel` case; no boolean interpolation case; no non-callable handler case; no write-failure case (read-only directory or full disk).

无大写/未知 minLevel 用例；无布尔插值用例；无「非 callable handler」用例；无写入失败（只读目录或满盘）用例。

## Verification protocol / 验证方式

- `./vendor/bin/phpunit` · `composer analyse` · `composer validate`
- Warning/notice/deprecation/risky flags in `phpunit.xml.dist`: all four on
- A PHP warning counts as a test failure only where those flags are on; otherwise run `./vendor/bin/phpunit --fail-on-warning` explicitly.
- 只有在上述开关打开时 PHP 警告才会导致套件失败；否则请显式加 `--fail-on-warning`。
