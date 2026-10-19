# Documentation adoption — Montero Dental Data Table UX Skill

**Status:** READY_FOR_REVIEW / DOCUMENTATION INSTALLATION COMPLETE; application BUILD remains unauthorized. **Date:** 19 October 2026 (Asia/Manila). This document and the accompanying Skill do not themselves authorize application implementation, Git writes, live-data testing, or deployment. The current user separately authorized this six-file documentation installation, the named branch, staging/commit, one push and one new Draft PR targeting `main`; no existing PR update, merge or deployment is authorized.

## Objective

Convert the provided UX/table tutorials and independent accessibility guidance into a source-aware, enforceable design/verification layer for Montero Dental's existing Bootstrap/PHP HRIS, payroll, and admin tables. Make future presentation increments predictable without rewriting functional query/authorization/financial systems.

## Proposed files

```text
.agents/skills/montero-dental-data-table-ux/
  SKILL.md
  references/
    rules.md
    patterns.md
    verification.md
    video-evidence.md

docs/work-items/frontend-modernization/
  DATA-TABLE-UX-SKILL-ADOPTION.md
```

These six files are installed for review under the explicitly approved documentation-only milestone on `docs/montero-data-table-ux-skill`. Existing files need not be changed to read this Skill directly by its path. A later approved docs task may add a one-line cross-reference to the frontend Skill / `AGENTS.md` if desired; do not edit them without an allowlist.

## Installation baseline and corrections

- Fresh `git fetch --no-tags origin main`, `git ls-remote --heads origin main` and GitHub `commits/main` API independently agree on `94c5d9dd99b9d61a2421863865236cf549770861`, the merged Stage 2A frontend foundation. It matches the compatibility-review checkout; no unexpected baseline changes were found. This is the accepted development baseline for this documentation installation, not production acceptance.
- All six exact destinations were checked absent before copying. All 170 pre-existing tracked files were fingerprinted for preservation checks. The package SHA-256 is `e9a7c55fc770dfba5684c5d005757c3892cd92209d235587c81b2f3e72129786`.
- Installation count is six. The archive-level `README.md` and `CODEX-PROMPTS.md` are excluded; no root README is installed or overwritten.
- README-only correction: its shorthand evidence path does not resolve from the archive root. The accurate repository-relative path is `.agents/skills/montero-dental-data-table-ux/references/video-evidence.md`. The excluded README remains unchanged.
- Prompt-only correction: its request for `REVIEW.md` is not an extra file authorization. Return the consolidated review in chat; create no seventh file. The excluded prompt file remains unchanged.
- Screen mappings distinguish the Offices tree from its employee-detail table and explicitly include Holidays and Shifts. Source paths are repository-relative references, never implementation allowlists.
- Historical Stage 1 SHAs below remain historical. Future work must reverify its then-accepted baseline and recognize existing foundation styling rather than replaying it.
- Third-party transcripts are potentially machine-generated, not authenticated publisher transcripts or footage review. Videos 1 and 5 remain unverified. Trailing whitespace has been removed.
- Compatibility remains subordinate to `montero-dental-frontend-design` and the checked-in Pearl & Teal design system and plan, including the requested Premium Edition direction only through those approved definitions. Preserve PHP/MVCLite, Bootstrap 5, installed Bootstrap Icons, jQuery/AJAX and existing DataTables; introduce no new dependencies, features or table functionality.
- Draft PR #11 and Draft PR #8 are outside this task and must remain untouched. No access to either PR is needed for this installation.

## Relationship to Stage 1 and future implementation

1. Stage 1 on `docs/frontend-pearl-teal-stage1` was documented as **documentation-only**, anchored to then-verified `main` at `01c9d5e0f9c1e391ee5bb74a40879d83c864c4dc`. Neither its old baseline nor this package can authorize future BUILD. Check fresh main and working tree before new work; do not reset current code to this SHA.
2. The approved Pearl & Teal design system remains the source of visual tokens, focus, responsive shell and Bootstrap mapping.
3. `montero-dental-frontend-design` remains the governing frontend process and safety Skill. The new table-specific Skill narrows and operationalizes table presentation requirements.
4. Future frontend increments already planned (employee table; HRIS catalogs/DTR/leaves; payroll; admin) are the natural insertion points. No separate sweeping table rewrite is needed.
5. If a video inspires new filter logic, mobile record cards, column toggles, row selection or other functional features, specify them independently and obtain separate authorization. Styling authority is not sufficient.
6. Existing employee 6-column contract, 5 sorts, search/Clear, pagination, 4 action definitions, generated rows and permissions must be preserved in frontend styling increments.

## Suggested stages

### A. Adopt documentation (separately authorize)

- Check repo status/current branch and source/readme; check for conflicting edits or prior Skill implementation.
- Install only the six Markdown files above (five Skill files plus this adoption document) into a reviewed docs branch/allowlist if expressly authorized; no application files, Git staging or publication unless expressly authorized.
- Validate frontmatter, relative Markdown links, existing design system token parity and no contradictory Git autonomy or functional authority.
- Produce the Table UX inventory of actual existing listing screens and record presentation-only candidates vs functional proposals.
- REVIEW with diff, checks and evidence. No claim of live UI passes.

### B. Apply styling during separately approved existing frontend increments

- Draft a Table UX Contract per target table.
- Identify the exact authorized CSS/static-view markup and protected executable regions.
- Implement small scopes; preserve all columns and existing behavior.
- Run the parent frontend Skill's full preservation/runtime/accessibility/print matrix, plus the [table verification matrix](../../../.agents/skills/montero-dental-data-table-ux/references/verification.md).
- Stop for review after each authorized increment.

### C. Evaluate optional advanced UX in separate specs

Treat column-scoped filter menus, mobile alternative card view, persisted row selection/column choice, advanced sorting and new bulk operations as independent usability+engineering changes requiring appropriate functional/security review. Do not silently bundle them into visual modernization.

## Source limitation

Video #1 `w0P8ygbuiZU` and video #5 `Ksx9C2-3yMo` were not reliably retrievable for direct content validation. Of the remaining three videos, publisher pages verified matching video IDs; one third-party, potentially machine-generated transcript supplied provisional points, with accuracy unverified against footage. No direct footage verification was performed. These rules are a carefully sourced and project-adapted **v0.1**, not a claim to capture literally every technique from all five videos. Update the [source-evidence ledger](../../../.agents/skills/montero-dental-data-table-ux/references/video-evidence.md) and the applicable MTD tests when authenticated transcripts or user notes become available.

## Documentation verification record

Verification uses archive comparison, existing PyYAML, local path resolution, rule-ID checks, pre-existing-file SHA-256 comparisons and Git diff/index inspection. No dependency installation or application service is needed.

| Check | Result | Evidence / scope |
|---|---|---|
| Archive integrity and destination absence | PASS | ZIP CRC check; exactly six allowlisted destinations absent before copying |
| Exact six-file inventory and protected files | PASS | All six additions are Markdown; 170 pre-existing tracked file hashes unchanged; no package README, prompts or extra report installed |
| Rules | PASS | MTD-01–30 unique/sequential; all 30 rule rows unchanged; 24 MUST, five SHOULD, one MAY |
| Frontmatter, links and token parity | PASS | Existing YAML parser; relative links and explicit source paths resolve; ten referenced colors match the approved design system |
| Whitespace and staged allowlist | PASS | `git diff --check`, `git diff --cached --check`; exact six additions and no other staged files |
| Scope and compatibility | PASS | Full six-file review; no executable payload, new dependency, functional authority or application change |
| Application/runtime/accessibility/database/payroll/print tests | NOT RUN | Documentation-only installation; unnecessary for this scope |

The consolidated final chat review supplies the commit SHA and new Draft PR URL after publication; no commit hash is embedded here because this document belongs to that commit. Publication stops at the Draft PR, with merge/deployment and later application work requiring separate authorization.

## Definition of done — DOCUMENTATION ADOPTION

- [x] Exact authorized documentation-only allowlist respected
- [x] Existing parent Skill and Pearl & Teal system unchanged unless separately allowlisted
- [x] Internal links, YAML frontmatter and Markdown syntax verified
- [x] No CSS/PHP/JavaScript, dependency, config, backend or database change
- [x] Rule IDs, table patterns, test matrix and video provenance remain traceable
- [x] Cross-module/policy/print restrictions clearly remain in force
- [x] Review handoff provides file list/diff, evidence, limits and next authorization step
- [x] No commits, pushes, pull requests, merges, staging, branch switching or deployment without the applicable human approval
