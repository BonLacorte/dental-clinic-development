# Evidence ledger — Five requested videos and supplemental guidance

Last research: 19 Oct 2026. Keep sources distinguishable from proposed product rules. **Do not claim every video was viewed or transcribed.** Three IDs below were matched to publisher pages containing their embedded video IDs. No direct footage verification was performed. Publisher descriptions do not establish footage review. Video 2 has an indexed third-party, potentially machine-generated transcript whose accuracy has not been verified against the footage; apparent transcription errors require cautious paraphrase. It supports provisional design ideas rather than authenticated quotations or proof of demonstrations. Two links remain unverified for subject/title/content.

| Video # | User URL | Verification state | Supported topic / safe extraction |
|---|---|---|---|
| 1 | https://www.youtube.com/watch?v=w0P8ygbuiZU | **UNVERIFIED** — no reliable matching title, publisher description or transcript retrieved | Do not attribute specific lessons. Seek title, transcript or timecodes; integrate only after review. |
| 2 | https://www.youtube.com/watch?v=IPH8R17w3vA | **PUBLISHER MATCH CONFIRMED** — [UX Movement: UI Design Tips for a Better Data Table UX](https://uxmovement.substack.com/p/ui-design-tips-for-a-better-data) embeds this exact ID. [Third-party, potentially machine-generated transcript](https://lilys.ai/ko/notes/775013), unverified against footage, describes header contrast, sort cues, lighter row lines, larger row spacing, icons, numeric alignment, statuses, dates, record identity, search, selected rows and bulk-action signaling. | Adapt these as MTD-03–19, with critical accessibility/semantic and existing-behavior qualifications. Do not assume every tip is universally correct. |
| 3 | https://www.youtube.com/watch?v=HZw9vUHfzpM | **PUBLISHER MATCH CONFIRMED** — [UX Movement: Why Data Table Filters Work Better in Headers](https://uxmovement.substack.com/p/why-data-table-filters-work-better) embeds this exact ID; only a brief public description was available. | Filter proximity/discoverability is a proposal, not proof that modifying existing filter mechanics is safe. MTD-15. Do not claim detailed steps from unseen footage. |
| 4 | https://www.youtube.com/watch?v=Oh6_V8q3Xfw | **PUBLISHER MATCH CONFIRMED** — [UX Movement: How to Make Data Tables Look Great on Mobile](https://uxmovement.substack.com/p/how-to-make-data-tables-look-great) embeds this exact ID and advocates card adaptation for mobile. | Cards can aid record-level reading (MTD-24). A reader comment on the publisher page raises a counterpoint: cards can make comparisons and 20+ fields harder. This is reader opinion, not publisher research or verified footage. Thus Montero defaults to table-local scroll and requires separate authorization for cards. |
| 5 | https://www.youtube.com/watch?v=Ksx9C2-3yMo | **UNVERIFIED** — no reliable matching title, publisher description or transcript retrieved | Do not attribute specific lessons. Seek title, transcript or timecodes; integrate only after review. |

## Independent authoritative corroboration

- [W3C WAI sortable table example](https://www.w3.org/WAI/ARIA/apg/patterns/table/examples/sortable-table/): native HTML table, header buttons, `aria-sort` on currently sorted header, visible focus; illustrative code not automatically production-ready.
- [W3C Understanding Reflow](https://www.w3.org/WAI/WCAG21/Understanding/reflow): tabular content may require two-dimensional scrolling; contain it within a local scroll surface so headings/search/pagination still reflow.
- [USWDS Table guidance](https://designsystem.digital.gov/components/table/): semantic headers, scrollable keyboard focus, stacked mobile variants for suitable tables and testing requirements.
- [NNG Data Tables: Four Major User Tasks](https://www.nngroup.com/articles/data-tables/): finding, comparing and filtering tasks, including clear active-filter indication.
- [W3C WCAG 2.2](https://www.w3.org/TR/WCAG22/): contrast, reflow, focus, and keyboard requirements.

The MTD rules combine these sources with adopted Montero judgment and preservation requirements. Tokens, density defaults, exact dimensions and functional-authorization boundaries are project decisions, not claims that every rule was demonstrated in a video. NN/g's tasks are finding matching records, comparing data, viewing/editing/adding one record, and acting on records; Montero's find / scan / compare / act framing is an adaptation.

## Suggested source-enrichment procedure (only if real transcripts supplied or verified)

For each video, record timestamp, **your own paraphrase** of the demonstrated principle, before/after change, use case, tradeoff, accessibility caveat, confidence, rule ID mapping, and whether it needs protected application logic. Distinguish video claims from adopted Montero requirements. Do not import long transcript quotations or copyrighted video content into the repository.

```text
Video ID / title:
Verified publisher / transcript URL:
Timestamp:
Observed principle (paraphrase):
Why it helps users:
When it can fail:
Scope class: presentation / functional proposal / not applicable
MTD rule number or proposed revision:
Montero screen/use case:
Verification test:
```
