# migears-log — Known Issues / 已知问题

> Generated from the miGears Full-Module Code Review Report (4th round, 2026-09-27).
> This file has two regions. Everything above **Owner feedback** is generated from the report — do
> not edit it there. The **Owner feedback** region belongs to the module maintainer: write into it,
> and it is preserved verbatim when the file is regenerated.
> A `fixed` reply is verified against the code by the reviewer before the finding is closed; a
> `rejected` reply is either accepted as a false positive or answered with counter-evidence.
>
> 本文件分两个区域。**「负责人反馈」之前的全部内容**由评审报告生成，请勿在该区修改；
> **「负责人反馈」区**归模块负责人所有，重新生成时会原样保留。
> 标注 `fixed`（已修复）的回复会被评审对照代码核实后才关闭；标注 `rejected`（不认同）的，
> 评审要么采纳为误报，要么给出反驳证据。
>
> 摘自 miGears 全模块代码评审报告（第四轮，2026-09-27）。

| | |
|---|---|
| Status / 状态 | **P0 cleared / P0 已清零** |
| Findings / 问题 | P0 0 · P1 0 · P2 3 · P3 1 |
| Size / 体量 | src 172 lines (89 net) · 27 tests · 1 src file |

Legend / 图例 — **P0** functional or security · **P1** documentation that fails when copied · **P2** robustness · **P3** metadata and docs
级别说明 — **P0** 功能性或安全级 · **P1** 文档照抄即错 · **P2** 健壮性 · **P3** 元数据与文档

## Verdict / 结论

The smallest module and the only one where nothing moved since the last round. All four previous items are still present, and the two worst are silent: an uppercase `minLevel` disables the threshold entirely, and a boolean `false` in the context prints as empty.

全仓最小，也是本轮唯一「一条都没动」的模块。上一轮四项全部原样存在，其中最糟的两条都是静默的：大写 minLevel 会让阈值彻底失效，布尔 false 在上下文里打印成空。

## Fixed since the last round / 本轮已修复确认

无。上一轮 4 项在本轮全部原样保留——这是本轮唯一一个「一条都没动」的模块。 

## Open findings / 未修问题


### P2

**P2-1** — `src/Logger.php:49,123`

- EN: The constructor declares `$handler` as `mixed` with zero validation; a non-callable surfaces only at invocation as `Error: Call to undefined function not callable()`.
- 中文: 构造器把 $handler 声明为 mixed 且零校验；非 callable 只在调用时以 Error: Call to undefined function not callable() 暴露。
- Verification / 验证: reproduced / 已实证

**P2-2** — `src/Logger.php:53,114`

- EN: An unknown or uppercase `$minLevel` disables thresholding: `LEVELS[$minLevel] ?? 0` makes `new Logger($h, "INFO")` yield 0, so even `debug()` is written. Conversely `log("INFO", ...)` degrades to debug and is dropped when `minLevel="info"`.
- 中文: 未知或大写的 $minLevel 会让阈值失效：LEVELS[$minLevel] ?? 0 使 new Logger($h, "INFO") 得到 0，于是连 debug() 都会被写出。反之 log("INFO", ...) 退化为 debug，在 minLevel="info" 时被丢弃。
- Verification / 验证: reproduced / 已实证

**P2-3** — `src/Logger.php:69,88,82-92`

- EN: `file_put_contents`/`fwrite` return values are discarded, so a full disk, bad permissions or a missing directory silently lose the log; `toStream()` does not verify its argument is a resource.
- 中文: file_put_contents/fwrite 的返回值被丢弃，磁盘满、权限不足或目录不存在都会静默丢日志；toStream() 不校验参数是否为 resource。
- Verification / 验证: static / 仅静态推断


### P3

**P3-1** — `src/Logger.php:142-143,159`

- EN: A boolean `false` context value interpolates to an empty string (`info("flag={flag}", ["flag" => false])` prints `flag=`), only `null` is special-cased; `format()` keeps a dead `$context` parameter; and the README still says "~150 lines" against 172.
- 中文: 布尔 false 的上下文值插值成空串（info("flag={flag}", ["flag" => false]) 输出 flag=），只有 null 被特判；format() 保留了一个死参数 $context；README 仍写 ~150 行而实际 172 行。
- Verification / 验证: reproduced / 已实证

## Test gaps / 测试盲区

No uppercase/unknown `minLevel` case; no boolean interpolation case; no non-callable handler case; no write-failure case (read-only directory or full disk).

无大写/未知 minLevel 用例；无布尔插值用例；无「非 callable handler」用例；无写入失败（只读目录或满盘）用例。

## Verification protocol / 验证方式

- `./vendor/bin/phpunit` · `composer analyse` · `composer validate`
- Warning/notice/deprecation/risky flags in `phpunit.xml.dist`: none on
- A PHP warning counts as a test failure only where those flags are on; otherwise run `./vendor/bin/phpunit --fail-on-warning` explicitly.
- 只有在上述开关打开时 PHP 警告才会导致套件失败；否则请显式加 `--fail-on-warning`。

## Owner feedback / 负责人反馈

<!-- OWNER-FEEDBACK:BEGIN -->
<!-- 渠道说明 / channel notice — 跨模块协调人发布，长期有效 / issued by the cross-module coordinator, standing
     ISSUES.md 是本模块「完整」的问题讨论与修复渠道，不只是评审结论的存放处。
     ISSUES.md is this module's COMPLETE issue-discussion-and-fix channel, not merely where review verdicts land.

     1. 每位负责人只对自己模块负责。对别的模块有意见、疑问、反证或改动建议，写入「对方模块」的 ISSUES.md，
        不要写在自己模块里。
        Each owner is responsible for their own module only. Opinions, questions, counter-evidence and
        change requests about ANOTHER module go into THAT module's ISSUES.md, never into your own.
     2. 在对方模块的文件里注明你是谁：模块名 + 身份。署名是硬要求，不署名则无法追溯来源。
        Sign it in the other module's file: your module name and your role. Signing is mandatory; an
        unsigned entry cannot be traced back to its author.
     3. 署名格式 / signature forms, so the source is distinguishable:
          reviewer — migears-full-review   评审方
          coordinator — cross-module       跨模块协调人
          owner — migears-<module>         其他模块负责人
     4. 结论文本一律带状态词：accepted / fixed / rejected / deferred / question / new-evidence。
        无署名条目下一轮可能被按新发现重新评级。
        Sign conclusions with one status word: accepted / fixed / rejected / deferred / question /
        new-evidence. An unsigned entry may be re-graded as a new finding in the next round.
     5. 开工之前先通读本文件：把每条开启条目按证据评估（签名条目也算），再把你接受的条目与自己的工作一并执行，
        不要拆成两轮。每条都要有状态词。
        Read this file before starting work: evaluate every open item on its evidence, signed entries
        included, then execute the ones you accept together with your own work in one pass. Every item
        gets a status word. -->

<!-- Maintainers: reply under each finding's `### <id>` heading and keep the headings, so the
     reviewer can map your reply to the finding. Status vocabulary, one word followed by your
     reasoning and any evidence:
       accepted      you agree; it will be fixed
       fixed         you believe it is already fixed in the code (the reviewer verifies this)
       rejected      you disagree — give the reason; the reviewer either accepts it as a false
                     positive or answers with counter-evidence
       deferred      deliberate, out of scope for now — give the reason
       question      you need a decision or clarification first
       new-evidence  you have additional facts bearing on the finding
     You may also add findings of your own under `### New — <short title>`.

     负责人：请在对应 `### <编号>` 标题下逐条回复，并保留标题以便评审对应。
     状态词（一个词 + 理由与证据）：
       accepted      认同，将会修复
       fixed         认为代码里已经修好（评审会对照代码核实）
       rejected      不认同——请给理由；评审要么采纳为误报，要么给出反驳证据
       deferred      有意暂缓或超出范围——请给理由
       question      需要先明确或决策
       new-evidence  补充与本次结论相关的新事实
     也欢迎在 `### New — <简短标题>` 下补充你发现的问题。 -->

### P2-1
<!-- 负责人反馈 / owner response here -->

- `fixed` — the constructor is now typed `callable $handler` (`src/Logger.php:51-52`), so a non-callable is rejected by the language at construction instead of surfacing as `Call to undefined function not callable()` at invocation. Commit `00817cc` ("Reject bad handlers and levels, and stop losing writes"). / 构造器现在声明为 `callable $handler`，非 callable 在构造期即被类型系统拒绝。提交 `00817cc`。
- Evidence / 证据: `./vendor/bin/phpunit` → `OK (36 tests, 47 assertions)`. / 运行 `./vendor/bin/phpunit` → `OK (36 tests, 47 assertions)`。
- owner — migears-log

### P2-2
<!-- 负责人反馈 / owner response here -->

- `fixed` — the constructor normalises the level (`strtolower`) and now throws `InvalidArgumentException: Unknown log level "..."` for anything outside the eight PSR-3 levels (`src/Logger.php:58-64`); `log()` normalises with `strtolower` too (`src/Logger.php:139`). Upper-case `"INFO"` therefore no longer collapses to priority 0, and neither path silently degrades. Commit `00817cc`. / 构造器归一化并抛未知级别异常；`log()` 同样归一化。大写 `"INFO"` 不再退化为 0。
- owner — migears-log

### P2-3
<!-- 负责人反馈 / owner response here -->

- `fixed` — `toFile()` checks the `file_put_contents()` result and throws `RuntimeException` on failure (`src/Logger.php:79-86`); `toStream()` rejects a non-resource argument and checks `fwrite()` (`src/Logger.php:103-111`). A full disk / missing directory / bad handle is no longer silent. Commit `00817cc`. / 写入失败与非法 resource 均改为抛异常，不再静默丢日志。
- owner — migears-log

### P3-1
<!-- 负责人反馈 / owner response here -->

- `fixed` — booleans are now spelled out (`true`/`false`) during interpolation (`src/Logger.php:167-170`); `format()` no longer carries the dead `$context` parameter (`src/Logger.php:186`); the stale "~150 lines" claim in the README was corrected. Commit `9109a55` ("Fix stale line-count claims and document boolean interpolation"). / 布尔值显式输出、死参数删除、README 行数修正。提交 `9109a55`。
- owner — migears-log
<!-- 跨模块条目 / cross-module items — 由跨模块协调人提出，非本轮评审 finding。口径见工作区根目录 `migears-engineering-gates.md`。
      Filed by the cross-module coordinator, not by the round's review. Standard: `migears-engineering-gates.md` at the workspace root. -->

### G2

- EN: Strict flags: `phpunit.xml.dist` currently sets none of the five. The standard is all five — `failOnWarning`, `failOnNotice`, `failOnDeprecation`, `failOnRisky`, `beStrictAboutOutputDuringTests` — which 11 of 27 modules set. Missing here: `failOnWarning`, `failOnNotice`, `failOnDeprecation`, `failOnRisky`, `beStrictAboutOutputDuringTests`. Turn them on and make the suite green; run `./vendor/bin/phpunit` and `composer analyse` before and after, and expect the first run to surface real warnings. If a flag genuinely cannot be turned on, reply `deferred` with the failing test and the reason instead of leaving the suite red.
- 中文: 严格开关：`phpunit.xml.dist` 目前五个开关一个都没开。标准是五个全开——`failOnWarning`、`failOnNotice`、`failOnDeprecation`、`failOnRisky`、`beStrictAboutOutputDuringTests`——27 个模块中 11 个如此。本模块缺 `failOnWarning`、`failOnNotice`、`failOnDeprecation`、`failOnRisky`、`beStrictAboutOutputDuringTests`。请打开并让套件保持全绿；改动前后各跑一次 `./vendor/bin/phpunit` 与 `composer analyse`，第一次跑出真警告是预期内的。若某个开关确实无法打开，请回复 `deferred` 并给出失败的用例与原因，而不是把套件留在红灯状态。
- Reply with one status word (`accepted` / `fixed` / `rejected` / `deferred` / `question`). / 请回复一个状态词（`accepted` / `fixed` / `rejected` / `deferred` / `question`）。
coordinator — cross-module

- `fixed` — all five strict flags are now `true` in `phpunit.xml.dist`: `failOnWarning`, `failOnNotice`, `failOnDeprecation`, `failOnRisky`, `beStrictAboutOutputDuringTests`, plus the matching `displayDetailsOnTestsThatTriggerWarnings` / `…Notices` / `…Deprecations` (the shape used by `migears-data-structure`). Every pre-existing attribute and the `<testsuites>` / `<source>` blocks are unchanged. / 五个严格开关全部打开，并按参考形态补齐三个 `displayDetails…` 属性；原有属性与结构未动。
- Evidence / 证据:
  - before / 改动前: `./vendor/bin/phpunit` → `OK (36 tests, 47 assertions)`, exit 0.
  - after / 改动后: `./vendor/bin/phpunit` → `OK (36 tests, 47 assertions)`, exit 0 (the suite was already clean, so the flags cost nothing — as with `migears-sql`).
  - flags are live / 开关确实生效: a temporary probe test that `echo`ed and raised `E_USER_NOTICE` made the run exit 1 (`There was 1 risky test … printed unexpected output` and `1 test triggered 1 notice`); the probe was then deleted and the suite re-ran green.
  - `./vendor/bin/phpstan analyse --no-progress` → `[OK] No errors`.
- The change is committed in the migears-log gate commit that carries this file (hash in the module hand-off). / 改动随本文件所在的那次 migears-log 门禁提交（哈希见交接报告）。
- owner — migears-log

<!-- OWNER-FEEDBACK:END -->
