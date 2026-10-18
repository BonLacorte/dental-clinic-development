# Montero Dental — Pearl & Teal design system

**Approved direction:** Direction 01 — Pearl & Teal. **Stage:** documentation only, 18 October 2026 (Asia/Manila). No CSS or application implementation is authorized by this document.

Read the [frontend Skill](../../../.agents/skills/montero-dental-frontend-design/SKILL.md), [implementation plan](FRONTEND-ONLY-IMPLEMENTATION-PLAN.md) and [Stage 1 handoff](STAGE-1-HANDOFF.md). Existing PHP/MVCLite, Bootstrap 5.3.3, jQuery 3.7.1/AJAX and current application contracts remain the foundation. No remote font, JavaScript framework, build system or application dependency is introduced.

## Approved tokens

These values are the source of truth for future allowlisted, scoped CSS. Custom-property names below are proposed mappings, not installed CSS.

| Token | Value | Proposed custom property | Presentation role |
|---|---|---|---|
| Primary | `#11685D` | `--montero-primary` | Primary action, link and selected-state text |
| Primary hover | `#0D544B` | `--montero-primary-hover` | Hover for primary actions |
| Primary active | `#09473F` | `--montero-primary-active` | Pressed primary action |
| Primary tint | `#E6F3EF` | `--montero-primary-tint` | Restrained navigation selection and supporting surfaces |
| Page canvas | `#F4F7F7` | `--montero-canvas` | Application page background |
| Surface | `#FFFFFF` | `--montero-surface` | Cards, controls, dialogs and bright sidebar |
| Primary text | `#193330` | `--montero-text` | Headings, labels, body and table text |
| Muted text | `#5C716D` | `--montero-muted` | Existing help text and secondary information |
| Divider | `#D9E4E1` | `--montero-divider` | Decorative separators and card/table boundaries |
| Control border | `#738C85` | `--montero-control-border` | Required visible input/control boundaries |
| Focus | `#087F8C` | `--montero-focus` | Visible keyboard focus against a suitable adjacent surface |

Use surface text on solid primary buttons and primary text on tinted selection backgrounds. Divider is not a substitute for the stronger control border or focus indicator. Validate rendered contrast in every changed state, including hover, pressed, selected, disabled and validation feedback. A teal outline directly against a teal button may lack contrast; preserve a visible surface-colored gap or a suitable contrasting treatment and measure the actual result. Semantic danger/warning/success/info colors retain existing meanings; do not recolor every business status teal.

## Typography, spacing and shape

- Use system sans-serif typography, such as `system-ui, -apple-system, "Segoe UI", sans-serif`, consistently within the changed scope. No remote font dependency. Base interface text is approximately **16px**, with a readable line height around 1.5; use relative sizes that respond to text zoom. Keep labels and help text readable rather than shrinking dense financial tables to fit.
- Spacing scale: **4, 8, 12, 16, 24, 32 and 48px**. Use 4/8px inside small groups, 12/16px between controls, 24/32px between sections and 48px only where the available space supports it. Allow wrapping and content-driven height.
- Card radius: **14px**. Form-control radius: **8px**. Use restrained depth, for example a future subtle `0 2px 8px` shadow with low-opacity primary text color, together with visible borders. Do not use shadow as the sole control boundary.
- Minimum **44px** interactive targets are the design objective, including icon actions, pagination and dialog close controls. Preserve labels and behavior while increasing usable area; avoid overlapping targets. Measure actual dimensions in future BUILD verification.

## Shell, navigation and responsive layout

Use the page canvas behind white surfaces. The sidebar is bright, with primary text, a divider and restrained teal/tint active treatments. Selection must remain distinguishable through existing text/current-page semantics as well as color. Navbar/footer styling should be quiet and consistent; preserve the current account display, toggle and Logout destinations and all existing visibility predicates.

Retain the approximately **250px** desktop sidebar and existing **992px** Bootstrap `lg` sidebar/offcanvas transition. Preserve the single sidebar DOM, measured header-height offsets, desktop collapse choice, internal scrolling, overlay/backdrop/body-lock lifecycle and focus handling. Existing integration IDs (`sidebar`, `toggleSidebar`, `closeSidebar`, `wrapper`, `content-wrapper`, `content`) remain stable. Styling does not authorize changes to `main.js` or navigation PHP.

Present content from narrow mobile devices to large desktops using available width, wrapping toolbars/headings and shrinking flex children. Do not impose one narrow maximum width on every business screen. Wide tables scroll **locally**, with every business column retained; never hide columns or use page-wide `overflow-x: hidden` to mask overflow. Preserve the current employee form layout at below 768px, 768–1199px and at least 1200px, its below-576px fullscreen dialogs and reachable dialog footer.

## Reusable component treatments

| Component | Future presentation treatment | Preservation requirement |
|---|---|---|
| Application shell | Canvas background, white surfaces, restrained dividers, system type and fitting gutters | Existing wrappers/includes/asset order, header measurement, sidebar breakpoint and scrolling |
| Navigation | Bright sidebar; tinted active region with primary text; readable hover and visible focus | Existing destinations, active/current state, permission/module/tenant decisions, disclosures and keyboard behavior |
| Page headers | Clear heading hierarchy; existing description/actions grouped with wrapping gaps | No new data summaries, status labels, routes or actions |
| Cards | Surface background, 14px radius, divider border, subtle shadow and 16/24px padding as space permits | Existing content hierarchy and interactive regions; no new dashboard statistics |
| Tables | Readable headers, consistent cell padding, restrained row dividers/hover and wrapping long names | Columns/values/order, sort controls, data attributes, event delegation and local scroll region stay intact |
| Pagination | 44px target objective, clear active tint/primary treatment, visible focus and wrapping | Existing search/sort context, page size, current-page semantics and server pagination |
| Forms | 16px text, 8px radius, strong control border, clear existing labels/help/validation and focus | Names/IDs/types/values, hidden fields/CSRF, action/method, required/readonly/disabled attributes, serialization and form ownership |
| Buttons | Primary/hover/active tokens with surface text; outlined primary alternatives; readable disabled state | Existing labels, button types, destructive meaning, handlers, pending guards and destinations |
| Badges/status | Consistent spacing and legible existing textual status | Exact status text and meaning; existing business-to-status mapping and semantic colors; no invented states |
| Dialogs | White surfaces, restrained border/shadow, readable headings/fields, reachable header/footer | Bootstrap targets/lifecycle, focus trap/return, Escape/static backdrop, Stay/Discard, Delete Cancel and pending-save behavior |
| Loading/error/empty states | Style existing indicators, summaries, Retry and feedback consistently; distinguish failure from empty visually where already implemented | No new response interpretation, handler, retry policy or success claim; preserve existing announcements and entered values |
| Login | Canvas/surface/primary palette, system type, readable labels and existing centered content-driven card | Standalone local CSS; preserve POST `/`, names/CSRF, password masking, native validation, CSP/headers, lockout/session logic and lack of scripts |
| Print/payroll | Keep established print layouts and readable financial data; contain new screen styles outside print where necessary | Existing print content, values, page breaks, pagination, identities and print action remain unchanged; no print redesign in this stage |

## Future Bootstrap/CSS mapping

Put approved rules in the existing `public/assets/css/style.css`, scoped to existing `.admin-shell`, `.login-page` and narrower page/component classes such as `.employee-page`. Do not add root-wide resets, new stylesheets, a token build pipeline or a library upgrade. Existing dark navbar utilities and employee-specific rules require deliberate scoped overrides; do not assume changing inherited primary variables overrides hardcoded colors or Bootstrap utilities.

| Scope/component | Proposed mapping for a future BUILD |
|---|---|
| `.admin-shell` body | Map `--bs-body-bg`/`--bs-body-color` to canvas/text; `--bs-secondary-color` to muted; `--bs-border-color` to divider; `--bs-primary` to primary and `--bs-primary-rgb` to `17, 104, 93`. Use explicit scoped backgrounds where the intended surface differs from canvas. |
| Links | Map `--bs-link-color`/`--bs-link-color-rgb` to primary / `17, 104, 93`, hover equivalents to primary hover / `13, 84, 75`; preserve existing semantic emphasis and keyboard cues. |
| `.btn-primary` | Set component-local `--bs-btn-bg`, border and text variables for primary/surface, hover variables for primary hover, active variables for primary active, and readable disabled variables. Inherited `--bs-primary` alone does not update Bootstrap's precompiled button variants. |
| `.btn-outline-primary` | Map component text/border to primary and hover/active to corresponding solid primary states with surface text. Preserve danger/secondary action meanings. |
| `.card`, `.modal-content` | Set applicable component background/border variables to surface/divider and card radius to 14px. Keep geometry/overflow rules and form ownership intact; modal mechanics remain unchanged. |
| `.table`, existing table header classes | Set component-local `--bs-table-bg`, color, border and hover variables explicitly to surface/text/divider/tint. Preserve semantic status classes and existing sort controls; do not modify generated-row JavaScript to restyle buttons. |
| `.pagination` | Use component `--bs-pagination-*` text/background/border/hover/active/focus variables with primary, surface, divider and tint as appropriate; measure contrast and control boundaries. |
| `.form-control`, `.form-select` | Apply scoped 8px radius, control-border token and surface/text; preserve readonly/disabled distinctions and existing validation styling/attributes. Do not use divider for required input boundaries. |
| Sidebar/offcanvas | Map `--bs-offcanvas-bg`/color to surface/text and narrowly override existing sidebar link/active/hover colors. Preserve width, z-index, visibility, collapse/overlay classes and all JavaScript state. |
| Focus and validation | Use a clearly visible focus treatment based on the focus token, generally a 3px outline with separation from the control. Preserve invalid/valid semantic distinctions and textual feedback; do not erase validation borders or Bootstrap focus behavior without equivalent verified presentation. |
| `.login-page` | Apply the same conceptual tokens through local rules; login does not load Bootstrap, so Bootstrap-only variables cannot be its entire treatment. Preserve `style-src 'self'` and script-free CSP. |
| Print media | Scope future screen decoration to screen media or provide narrowly reviewed print preservation rules; compare actual existing print output before acceptance. Do not change print data, layout or page-break logic. |

These are mapping requirements, not runnable CSS. The future BUILD must inspect computed styles and specificity in the actual accepted baseline; static allowlisted view-class changes require the same preservation review as CSS changes.

## Accessibility and acceptance

Target **WCAG 2.2 AA for changed presentation**. Check normal-text contrast of at least 4.5:1, large text at least 3:1 and relevant control/focus graphics at least 3:1, plus visible unobscured focus, keyboard access, text zoom/reflow and unchanged validation cues. Keep the 44px design objective distinct from any claim of WCAG conformance. Preserve reduced-motion behavior and text equivalents for existing statuses.

Use the [verification reference](../../../.agents/skills/montero-dental-frontend-design/references/verification.md) for viewport, input, accessibility and regression evidence. Token selection is not proof of rendered accessibility; no runtime/accessibility tests were run in Stage 1.

## Historical direction and limits

The [UI-01 specification](UI-01-SPEC.md) and [A](UI-01A-HANDOFF.md)/[C](UI-01C-HANDOFF.md) handoffs retain historical blue-primary/dark-sidebar decisions and older baseline observations. Pearl & Teal supersedes those **visual palette** choices for future explicitly authorized styling; functional decisions, acceptance requirements and unresolved C-EMP gates remain in force. Do not replay A/B/C implementation, rewrite their historical records or import Draft PR #8. The approved direction changes no current CSS until a separate BUILD is authorized.
