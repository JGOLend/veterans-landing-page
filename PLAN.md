# Implementation plan - JGO Lending veterans landing page

Companion to `brief-veterans-landing-page.md`. The brief is law; this plan decides everything the brief left open. Where this plan and the brief disagree, the brief wins. Deliverable: one file, `veterans/index.html`, everything inline.

---

## 1. Document skeleton

Exact order of the file, top to bottom.

### `<head>`

1. `<meta charset="utf-8">`
2. `<meta name="viewport" content="width=device-width, initial-scale=1">`
3. `<title>The broker who speaks DVA. | JGO Lending</title>` (approved H1 verbatim, nothing invented)
4. `<meta name="description" content="...">` - the approved hero sub-line, character for character. No other copy may be used here.
5. `<meta name="theme-color" content="#0A0A0A">`
6. Font loading: `<link rel="preconnect" href="https://fonts.googleapis.com">`, `<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>`, then one stylesheet link for Montserrat 700;800;900 and Inter 400;600 with `display=swap` (see §2.7).
7. **Meta Pixel base snippet** - a `<script>` that first declares `const META_PIXEL_ID = "REPLACE_ME"; // [client to provide from Events Manager]`, then the standard fbq stub loader, then `init` + `PageView` wrapped in the guard described in §7.
8. **Pixel `<noscript>` fallback** - the standard 1px image, immediately after the pixel script. Its URL contains the pixel ID literally; mark it with an adjacent comment `<!-- PIXEL ID: also replace REPLACE_ME in this noscript URL -->` because the JS constant cannot reach it.
9. One `<style>` block - the entire stylesheet. Nothing external.

### `<body>`

All sections inside `<main>`, in this order, with these ids/classes:

| # | Section | Element | id | class |
|---|---|---|---|---|
| 1 | Hero | `<section>` | `hero` | `hero` |
| 2 | Your income isn't the problem | `<section>` | `problem` | `section s-problem` |
| 3 | Who reads your file | `<section>` | `file` | `section s-file` |
| 4 | What we actually speak | `<section>` | `speak` | `section s-speak` |
| 5 | FAQ | `<section>` | `faq` | `section s-faq` |
| 6 | Soldier On donation | `<section>` | `donation` | `section s-donation` |
| 7 | Testimonials | commented-out `<section>` | `testimonials` | `section s-testimonials` |
| 8 | Form | `<section>` | `enquire` | `section s-enquire` |
| - | Licence footer | `<footer>` | - | `footer` |

Between consecutive rendered sections (problem→file, file→speak, speak→faq, faq→donation, donation→enquire) sits a separator element `<div class="rule" aria-hidden="true"></div>` (§2.4). No separator above `#problem` (the hero's bottom edge is the break) and none above the footer (the footer is inside the page flow after `#enquire` with its own top hairline).

Section 7 ships exactly as:

```html
<!-- TESTIMONIALS: enable when real, attributable veteran testimonials are supplied -->
<!--
<section id="testimonials" class="section s-testimonials"> ... empty figure/blockquote scaffold, no copy ... </section>
-->
```

No placeholder quotes, names, stars or counts anywhere inside the comment.

After `</main>`: one `<script>` block at the end of `<body>` containing, in order: `const FORM_ENDPOINT = "REPLACE_ME"; // [client to provide]`, the module-scope pixel booleans, the FormStart listener, and the form submit handler. This is the only body script.

---

## 2. Visual system - decided

Brand tokens from brief §5 go verbatim in `:root`, plus `color-scheme: dark` on `:root` so native form controls (selects, checkbox) render dark without custom chrome. Derived tokens allowed: `--text-muted: #B3B3B3` (footer, footnote, trust line - 9:1 on the black ground), `--line: rgba(255,255,255,0.14)` (FAQ item separators only, so gold stays scarce).

### 2.1 Type scale

Montserrat for h1/h2/h3/eyebrow/CTA/credential items; Inter for everything else. Every `font-family` declaration carries the full fallback stack from the brief.

| Role | 375px | ≥900px | clamp() | Weight / treatment |
|---|---|---|---|---|
| H1 | 36px | 60px | `clamp(2.25rem, 1.19rem + 4.5vw, 3.75rem)` | Montserrat 900, line-height 1.05, letter-spacing -0.01em |
| H2 | 28px | 44px | `clamp(1.75rem, 1.05rem + 3vw, 2.75rem)` | Montserrat 800, line-height 1.1 |
| H3 (FAQ summaries, success heading, step titles) | 19px | 22px | `clamp(1.1875rem, 1.05rem + 0.6vw, 1.375rem)` | Montserrat 700, line-height 1.25 |
| Credential items (S3 list) | 24px | 34px | `clamp(1.5rem, 1.055rem + 1.9vw, 2.125rem)` | Montserrat 800, white |
| Body | 16px | 18px | `clamp(1rem, 0.91rem + 0.38vw, 1.125rem)` | Inter 400, line-height 1.6 |
| Small (trust line, footnote, labels, footer) | 13px | 14px | `clamp(0.8125rem, 0.77rem + 0.19vw, 0.875rem)` | Inter 400/600; footer at flat 13px |
| Eyebrow | 12px | 12px | fixed `0.75rem` | Montserrat 700, uppercase, letter-spacing 0.18em, colour `--jgo-gold-mid` (flat gold small accent - permitted use) |

### 2.2 Vertical rhythm

- Section padding: `padding-block: clamp(4.5rem, 2.5rem + 8vw, 7.5rem)` - 72px at 375px, ~112px at 900px, capped 120px. The hero is exempt (§3).
- Container: `.wrap { max-width: 1100px; margin-inline: auto; padding-inline: clamp(1.25rem, 4vw, 2.5rem); }` - 20px gutters at 375px.
- Text measure: body copy blocks capped at `max-width: 42rem`.
- Element gaps within a section: eyebrow→H2 `0.75rem`; H2→intro/body `1.25rem`; intro→list `2rem`; list items `1.75rem` apart (2rem at ≥900px); closing line `2.5rem` after the list. Space via `margin-top` on the following element, single direction, no collapsing surprises.
- Hero internal gaps: logo slot→eyebrow `2rem`, eyebrow→H1 `0.75rem`, H1→sub-line `1.25rem`, sub-line→CTA `1.75rem`, CTA→trust line `1rem`.

### 2.3 The gold gradient phrase - one per section, named

Class `.gx`: `background: linear-gradient(180deg, #FAF7C8 0%, #E4C377 42%, #9D722D 100%); -webkit-background-clip: text; background-clip: text; color: transparent;` wrapped in `@supports (background-clip: text) or (-webkit-background-clip: text)`, with a plain-white default outside the `@supports` so unsupporting browsers show white, never flat yellow. Applied to a `<span class="gx">` inside the heading; the remainder of every heading is white.

| Section | Heading | Gold phrase (exact) | Stays white |
|---|---|---|---|
| 1 Hero | H1 | `speaks DVA.` | `The broker who ` |
| 2 Problem | H2 | `The translation is.` | `Your income isn't the problem. ` |
| 3 File | H2 | `One loan file.` | `Four careers. ` |
| 4 Speak | H2 | `Fluent in DVA.` (entire H2 - it is the ad hook itself) | - |
| 5 FAQ | H2 `Questions people actually ask` | `actually ask` | `Questions people ` |
| 6 Donation | H2 | `$600 to Soldier On.` | `Mention the post. ` |
| 7 Testimonials | commented out | n/a | n/a |
| 8 Form | H2 | `About two minutes.` | `Five short questions. ` |

No other element on the page gets `.gx`. The FAQ answers, body copy, footer: all white or `--text-muted`.

(Section 5's H2 is the brief's own section title `Questions people actually ask` - the brief supplies no other heading for it.)

### 2.4 Gold hairline rules

Two constructions, both pure gradient backgrounds on empty elements, never `border` or `<hr>` default styling:

- **Section separator** `.rule`: height `1px`, width `100%` of `.wrap` (so max 1100px minus gutters), `background: linear-gradient(90deg, transparent 0%, #9D722D 18%, #E4C377 50%, #9D722D 82%, transparent 100%)`. Fades to nothing at both ends so it reads as a hairline, not a boxed border. Sits between sections with no extra margin (section padding provides the air).
- **Credential rule** `.cred-rule` (four of them, one above each S3 item, plus reused as the footer's top hairline at full width in separator form): width `56px`, height `3px`, `background: linear-gradient(90deg, #FAF7C8 0%, #E4C377 42%, #9D722D 100%)`, `margin-bottom: 0.75rem`, left-aligned. This is the signature gradient rotated horizontal, matching the S2 ad's short rules.

### 2.5 CTA button

**Decision: gold gradient block.** `background: linear-gradient(180deg, #FAF7C8 0%, #E4C377 42%, #9D722D 100%)`, text `--jgo-black`, Montserrat 800, `0.9375rem`, uppercase, letter-spacing `0.06em`, padding `1rem 2.5rem`, `border-radius: 2px`, `border: 0`, min-height 56px, no shadow, no transition gimmicks (hover: `filter: brightness(1.06)` only). Justification: the gold-gradient block is the single loudest element in the ad creative, so carrying it onto the two buttons is what makes the click feel continuous - a ghost/outline button with white type is exactly the generic dark-SaaS default the brief bans. Contrast of `#0A0A0A` on the darkest stop `#9D722D` is ~5.4:1, passing 4.5:1.

Hero CTA: inline-block, full-width below 480px capped at `20rem`. Form submit: full width of the form column. Both read `Get Started`. These two buttons and the checkbox tick are the only gold *fills* on the page.

### 2.6 Focus ring

`:focus-visible { outline: 3px solid var(--jgo-gold-mid); outline-offset: 2px; }` globally, plus plain `:focus` (same rule) on `input, select, textarea, summary, button` so older mobile browsers still show it. Never `outline: none` anywhere. The flat gold-mid is permitted here by the brief's own token comment.

### 2.7 Fonts and failure mode

Load Montserrat **700, 800, 900** and Inter **400, 600** - five weights, nothing else - from Google Fonts via one `<link rel="stylesheet">` with `&display=swap`. `display=swap` means text renders immediately in the fallback stack and swaps when the font arrives; if the request fails outright, the page simply stays on `-apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif` permanently. To make that graceful: no fixed heights on any text container, no letter-spacing that only works with Montserrat's metrics, and the hero fold check (§3) must pass in the fallback stack too. No `@font-face` self-hosting (would bloat the single file past reason); the fonts link is one of the three permitted external calls.

### 2.8 Bans restated as CSS facts

No `box-shadow`, no `backdrop-filter`, no `border-radius` above 2px, no gradient anywhere except (a) the body's radial ground exactly as the brief specifies it, (b) the gold gradient in its three uses: `.gx` text, the two rule types, the CTA fill. No flat `yellow`/`gold` hex outside the four brand tokens. No icons, no emoji, no SVG decorations. FAQ markers are typographic (`+`/`−` via CSS `content` on `summary::after`, coloured `--text-muted`). Donation step numbers are CSS-counter numerals in Montserrat 800, `--jgo-gold-mid` (small flat-gold accent, permitted).

---

## 3. Layout behaviour

Mobile-first, single column everywhere by default. At `@media (min-width: 900px)`:

- **S3 `#file`**: built type-only single column (portrait absent at build time). Ship a commented-out `<figure class="portrait">` after the credential list and a ready CSS rule `.s-file.has-portrait .file-grid { display: grid; grid-template-columns: minmax(0, 3fr) minmax(0, 2fr); gap: 4rem; align-items: start; }`. The comment instructs: when `assets/james-portrait.jpg` arrives, uncomment the figure AND add class `has-portrait` to the section. Type-only, the section is deliberate on its own: eyebrow, H2, the four gold-ruled credentials at display size, closing paragraph - it mirrors the S2 ad frame, which is also type-only.
- **S6 `#donation`**: the three steps become `grid-template-columns: repeat(3, 1fr); gap: 2.5rem`. Below 900px they stack.
- **Everything else stays single column** with the 42rem measure - S2's three items, the FAQ, and the form (form fields capped at `max-width: 40rem`) deliberately never go multi-column; a wide form reads as SaaS.

### Hero above-the-fold guarantee (explicit verification item)

Mechanism: `.hero { min-height: 100svh; display: flex; flex-direction: column; justify-content: flex-start; padding: 1.5rem 0 3rem; }` with `100vh` declared first as fallback for browsers without `svh`. Content is **top-anchored, not vertically centred** - centring risks pushing the CTA down on short viewports. The stack at 375×667 (iPhone SE, worst common case) budgets: 24px top pad + 40px logo slot + 32px gap + 12px eyebrow + 12px + H1 at 36px × 2 lines ≈ 76px + 20px + sub-line 16px × ~5 lines ≈ 128px + 28px + button 56px = ~430px, leaving the trust line (~60px over 3 lines) and bottom padding inside 667px with margin to spare. The caps that make this hold: H1 clamp floor 36px, sub-line at body size not lead size, logo slot fixed 40px.

**What to check:** 375×667 device emulation, fonts loaded AND fonts blocked (fallback stack), H1 fully visible and the `Get Started` button fully visible without any scroll. Also spot-check 375×600.

---

## 4. `#enquire` landing

There is no sticky header, so an anchor jump lands the target's top edge at the viewport's top edge - the form section is inherently unobscured. Two guarantees anyway:

1. `#enquire { scroll-margin-top: 1.5rem; }` - 24px of breathing room so the section's heading is not flush against the viewport chrome and the landing feels placed, not slammed.
2. Section order inside `#enquire` is H2 → intro → form, with the section's top padding on the normal rhythm - so on a 375×667 viewport the H2, intro and first field (Name) are all visible on landing, which is the real "form visible" test.

Also set `html { scroll-behavior: smooth; }` for the in-page hero CTA jump; direct loads of `.../veterans#enquire` are unaffected by it. No JS scroll handling of any kind. Verify by loading `veterans/index.html#enquire` cold in a 375px viewport.

---

## 5. Missing-asset degradation

Both images are `[client to provide]` and absent at build time. **Mechanism: the `<img>` elements ship commented out.** This is the only approach with literally zero network requests, zero console 404s and zero broken-image icons - `onerror` hiding still logs a failed request, so it is not used.

- **Logo (hero, top-left):** `<div class="hero-logo" aria-hidden="true">` with fixed height `40px` (mobile) / `52px` (≥900px) acting as a spacer that keeps the hero rhythm identical before and after the file arrives. Inside it: `<!-- LOGO: uncomment when assets/jgo-logo-white.png is supplied. Do not recreate, redraw or substitute this logo. --><!-- <img src="assets/jgo-logo-white.png" alt="JGO Lending" class="logo-img"> -->`. CSS ready: `.logo-img { display: block; height: 40px; width: auto; }` with the 52px override at ≥900px. No text stand-in, no drawn mark, no favicon-as-logo. Empty space is the specified degradation.
- **Portrait (S3):** commented-out `<figure class="portrait"><img src="assets/james-portrait.jpg" alt="James O'Brien"></figure>` per §3, with the marker `<!-- PORTRAIT: uncomment and add class "has-portrait" to #file when assets/james-portrait.jpg is supplied. Do not substitute stock imagery. -->`. Absent, S3 renders as the type-only credential stack, which is a complete design, not a degraded one.

Handover note lists both uncomment steps.

---

## 6. Form and JS behaviour

### Markup

`<form id="lead-form" method="post" novalidate-NOT-set>` - native HTML5 validation stays ON (no `novalidate`), so the submit handler only runs on a valid form and empty-required-field messaging is free and accessible. Fields, in order, exactly as briefed - **no additions**:

1. `name` - `<input type="text" required autocomplete="name">`, label `Name`
2. `email` - `<input type="email" required autocomplete="email">`, label `Email`
3. `phone` - `<input type="tel" required autocomplete="tel">`, label `Phone`, **no `pattern` attribute** (brief: accept spaces and `+61`, do not over-validate)
4. `goal` - `<select required>` with a disabled empty first option and the five briefed options verbatim, label `What are you looking to do?`
5. `service_status` - `<select required>` with the four briefed options verbatim, label `Your service status`
6. `notes` - `<textarea>` optional, label `Anything we should know?`
7. `soldier_on` - `<input type="checkbox">` with label exactly `I saw the Soldier On post`

Labels are real `<label for>` elements above each field (Inter 600, small size). **Honeypot:** a wrapper `<div class="hp" aria-hidden="true">` styled `position:absolute; width:1px; height:1px; overflow:hidden; clip-path: inset(50%);` (not `display:none` - some bots skip hidden fields) containing `<input type="text" name="company" tabindex="-1" autocomplete="off">`. No CAPTCHA.

After the fields: `<p class="form-error" role="alert" hidden>` carrying the exact error copy including the visible `[EMAIL - client to provide]` placeholder, then the submit `<button type="submit">Get Started</button>`. As a sibling after the form: `<div class="form-success" hidden>` containing the exact H3 (`tabindex="-1"` for focus) and body success copy.

### State machine (prose)

States: **idle → in-flight → success** (terminal) or **→ error → idle-with-values**.

On `submit` event (fires only when natively valid):

1. `preventDefault()`.
2. **Honeypot check:** if `company` has any value, show the success state (swap per step 7) and `return` - no fetch, no pixel event, nothing logged. Silent discard that looks like success to the bot.
3. Enter in-flight: `submitBtn.disabled = true`, `form.setAttribute('aria-busy','true')`. Button label stays `Get Started` - no invented "Sending…" copy. Hide `.form-error` if visible from a previous attempt.
4. Build `FormData(form)`, delete the `company` entry, and `await fetch(FORM_ENDPOINT, { method:'POST', body: formData, headers:{ Accept:'application/json' } })` inside `try/catch`.
5. **Failure path** (`catch`, or `!response.ok`): un-hide `.form-error`, `submitBtn.disabled = false`, remove `aria-busy`. **Never call `form.reset()`, never clear any field** - the typed values survive untouched because nothing touches them. No `Lead`, no event of any kind. Back to idle-with-values.
6. **Success path** (`response.ok`): set `hidden` on the form, remove `hidden` from `.form-success`, move focus to the success H3. The page does not navigate; the success block occupies the form's place.
7. **Only now** - after the success state is visible - fire the `Lead` event (§7). The button is still disabled and the form hidden, so a second submission (and therefore a second `Lead`) is structurally impossible.

Order is load-bearing: disable **before** fetch, success state **before** `Lead`, re-enable **only** on failure.

---

## 7. Meta Pixel wiring

Two constants, each at the very top of its script block:

- `const META_PIXEL_ID = "REPLACE_ME";` - head, above the fbq stub.
- `const FORM_ENDPOINT = "REPLACE_ME";` - body script, first line.

**Guard:** `const PIXEL_ACTIVE = META_PIXEL_ID !== "REPLACE_ME";`. `fbq('init', META_PIXEL_ID)` and `fbq('track','PageView')` run only when `PIXEL_ACTIVE` - so the unconfigured local file produces zero console errors and sends no junk init. Every later `fbq` call is guarded by `PIXEL_ACTIVE && typeof fbq === 'function'`. (Pixel Helper verification therefore only becomes possible once the real ID is in - noted in §9.)

Event rules:

1. **`PageView`** - fires automatically via the base snippet on load. **Nothing else fires on load.**
2. **`FormStart`** - `fbq('trackCustom','FormStart')` on the first `focusin` anywhere inside `#lead-form`, guarded by a module-scope `let formStarted = false;` set true on first fire. Boolean, not storage - a reload legitimately re-arms it (once per page view, per the brief). Second and later focuses: nothing.
3. **`Lead`** - only in success-path step 7 of §6, exactly:
   ```js
   const eventId = (crypto.randomUUID && crypto.randomUUID()) || String(Date.now()) + Math.random();
   fbq('track', 'Lead', { content_name: 'veterans-enquiry' }, { eventID: eventId });
   ```
   The `eventID` is for later CAPI deduplication; `content_name` is the **only** parameter.

**Must NOT fire, ever:** `Lead` on button click, on page load, on the `#enquire` anchor jump, on a failed submit, on a honeypot-caught submit, or twice for one submission. No name, email, phone, service status, loan detail or any personal/financial value in any event payload. No other events, standard or custom, exist on this page.

---

## 8. Compliance checklist for the finished file

Run over `veterans/index.html` before handover:

1. **Grep - zero matches required** (case-insensitive) in rendered copy: `every loan`, `every settlement`, `guaranteed`, `pre-approved`, `approval guaranteed`, `no credit check`, `unlock`, `supercharge`, `seamless`, `journey`, `hassle-free`.
2. **Grep for `—` (U+2014) and `–` (U+2013): zero matches.** The brief's approved copy uses spaced hyphens (` - `) throughout - transcribe them exactly, character for character; do not "fix" them into dashes. The `·` middots in the eyebrow and footer are required characters, not violations.
3. **No rates or figures:** no interest rate, comparison rate, repayment figure, borrowing capacity, client count, settlement count, star rating or years-of-experience number anywhere. The only numerals permitted in copy are those inside brief §4 strings (`$600`, `17 apartments`, `Five short questions`, `two minutes`, `+61` in the phone note) plus the footer placeholders. CSS/JS numerals do not count.
4. **Tax silence:** the words `tax`, `tax-free`, `taxable`, `after-tax` appear nowhere.
5. **No endorsement implication:** Soldier On is referenced in text only, no logo, no insignia, no wording implying Soldier On/DVA/Defence sponsors or endorses JGO. Donation wording ties strictly to mentioning the post.
6. **Australian English:** enquiry/enquire, no US spellings in copy (CSS property names like `color` are code, exempt).
7. **Copy fidelity:** every hero, section, FAQ, form, success and error string diffs clean against brief §4, character for character, including full stops.
8. **Testimonials:** section exists only inside a comment beginning exactly `<!-- TESTIMONIALS: enable when real, attributable veteran testimonials are supplied -->`; nothing testimonial-like renders.
9. **Footer:** renders visibly with `[CR NUMBER - client to provide]`, `[AGGREGATOR LEGAL NAME - client to provide]`, `[ACL NUMBER - client to provide]`, `[Privacy Policy link - client to provide URL]` plainly readable, plus the general-information / approval-not-guaranteed paragraph verbatim from brief §9. (That paragraph's "approval is not guaranteed" is required legal wording and is the one sanctioned use of the word - the grep in item 1 must whitelist this exact footer sentence.)
10. **No forbidden tech:** no external script other than `connect.facebook.net`, no external CSS other than the fonts link, no library, no analytics, no consent banner.

---

## 9. Build order and verification

### Build order

1. `git init` in the project folder (it is not yet a repo), initial commit of the brief and this plan, then branch `build/veterans-landing-page`. All work on that branch; never touch anything outside `veterans/`.
2. Create `veterans/index.html`: head skeleton per §1 (title, meta, fonts link, pixel script with guard, noscript, empty style block).
3. Write the stylesheet: tokens, ground gradient, type scale, rhythm, `.gx`, rules, CTA, focus ring, form controls, honeypot hiding, 900px media block, `scroll-margin-top`.
4. Build sections 1-6 and 8 with copy pasted (not retyped) from brief §4, wrapping each section's one gold phrase per §2.3 table. Add the commented testimonials block, commented logo/portrait per §5, separators, footer with visible placeholders.
5. Build the form markup per §6, then the body script: constants, pixel guards, FormStart listener, submit state machine.
6. Self-review against §8 checklist, then run the verification below.
7. Handover note: "Upload `veterans/index.html` to the GoDaddy hosting as `/veterans/index.html` so it serves at `https://jgolending.com.au/veterans`" plus the brief §11 outstanding list (pixel ID in two places - constant and noscript URL - endpoint URL, error-state email, CR/aggregator/ACL, privacy URL, logo file, portrait file, Soldier On written confirmation).

### Verification - checkable locally now

- [ ] 375×667 emulation: H1 and `Get Started` both fully visible, no scroll - with webfonts and with fonts blocked
- [ ] H1 reads exactly `The broker who speaks DVA.`
- [ ] Opening `index.html#enquire` cold at 375px: form heading and first field visible, nothing overlapping
- [ ] Every string diffs clean against brief §4
- [ ] Empty required field → native validation message, no network request
- [ ] Point `FORM_ENDPOINT` at a test endpoint (e.g. a free Web3Forms/Formspree test key): valid submit swaps to success state in place, no navigation
- [ ] Point `FORM_ENDPOINT` at an invalid URL: error state shows, typed values all intact, no `Lead` in the network tab
- [ ] Honeypot filled programmatically: no network request occurs
- [ ] Zero console errors, zero 404s on load (commented-out images guarantee this)
- [ ] File size well under 500KB (expect ~30-40KB)
- [ ] Tab through the whole page: every field, both selects, the checkbox, both buttons and each FAQ `summary` reachable with the gold-mid focus ring visible
- [ ] Contrast: body white on `#0A0A0A` ≈ 20:1, muted `#B3B3B3` ≈ 9:1, black-on-gold CTA ≈ 5.4:1 - all ≥ 4.5:1
- [ ] §8 greps all clean
- [ ] Footer placeholders visible

### Verification - structural only until client supplies credentials

These need the real Pixel ID and endpoint; until then, verify the code path by inspection and defer live checks to pre-publish:

- [ ] Pixel Helper: `PageView` once on load, no errors *(needs META_PIXEL_ID)*
- [ ] `FormStart` once on first field focus, not again on second focus *(structurally: the `formStarted` boolean; live: needs ID)*
- [ ] `Lead` once, only after the success state, never on failure *(structurally: §6 step order; live: needs ID + endpoint)*
- [ ] No personal data in any pixel payload - network tab shows `content_name` only *(needs ID)*
- [ ] All three events in Events Manager → Test Events *(needs ID)*

The handover note must say plainly that the five pixel checks were verified structurally and must be re-run with Pixel Helper once the real ID is inserted, before the campaign goes live.
