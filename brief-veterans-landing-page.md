# Build brief - JGO Lending veterans landing page (/veterans)

**Where it lives:** One self-contained HTML file published to the existing GoDaddy hosting for `jgolending.com.au`, at the path `/veterans` (upload as `veterans/index.html` so the URL is `https://jgolending.com.au/veterans`).
**Branch:** Create a new branch named `build/veterans-landing-page`. Do not commit to the live branch.
**How it publishes:** Manual upload to GoDaddy after review. The builder's job ends at a working file plus a one-line note on which file to upload where. Do not attempt to deploy.
**Deliverable:** A single file. All CSS and JS inline. No build step, no framework, no package manager, no external scripts except the Meta Pixel and the form endpoint.

---

## 1. Objective

A Meta-traffic landing page for JGO Lending's veterans home loan campaign. Its single job is **enquiry form submissions**. Nothing else on the page competes with the form: no phone number as a primary CTA, no newsletter signup, no links off-site except the privacy policy and the required licensing text.

---

## 2. Audience and message match

- **Who arrives:** Australian veterans, ex-serving ADF, 25-60, Australia-wide. Includes medically discharged veterans on DVA and incapacity income, and CSC pension recipients. Partners of veterans arrive too and often drive the decision.
- **Traffic source:** Meta (Facebook and Instagram) feed and Reels, one consolidated campaign spanning five awareness phases. Assume **mobile-first - most traffic is a phone at ~375px wide.**
- **Two entry points, one page:**
  - Upper-funnel ads (Unaware, Problem Aware, Solution Aware) land on `https://jgolending.com.au/veterans` - top of page.
  - Lower-funnel ads (Product Aware, Most Aware) land on `https://jgolending.com.au/veterans#enquire` - **the page must have an element with `id="enquire"` on the form section, and it must land cleanly with the form visible, not hidden behind a sticky header.**

**The hooks to echo.** These are the exact promises the live ads make. Section 1 (hero) must echo the first one near enough to word-for-word that the click feels continuous:

| Ad | Promise made in the feed | Where the page answers it |
|---|---|---|
| S3 "Fluent in DVA" | "The broker who speaks DVA. Veteran-owned. DHOAS-accredited." | Hero headline + Section 4 |
| S2 "The Resume" | "Veteran. Ex-lender. Lawyer. Developer." | Section 3 |
| P1 / P2 (Problem Aware) | "Your income isn't the problem" / "Service shouldn't slow a home loan" | Section 2 |
| PA3 | "Can I even get a loan on DVA income?" - "Don't assume. Ask." | Section 5 FAQ + form intro |
| PA1 / M2 | "Mention this post when you enquire - $600 to Soldier On when it settles" | Hero sub-line + Section 6 + form checkbox |

---

## 3. Page structure, section by section

Eight sections in this order. One idea each. Mobile-first single column; at ≥900px the story and credential sections may go two-column.

**1. Hero** - Job: continue the ad's promise and get the scroll started. Contains: H1, one sub-line, primary CTA button (anchors to `#enquire`), and one quiet trust line. Logo top-left, small. Dark ground. No stock photography. Nothing below the fold competes with the CTA.

**2. "Your income isn't the problem"** - Job: name the pain the Problem Aware ads named. Three short items with a one-line intro. Plain text, no icons that look like clip-art.

**3. Who reads your file** - Job: the credential stack that makes JGO different. Four credentials as a stacked list with gold rules, matching the S2 ad layout. **Reserve a slot for a real photograph of James** (`assets/james-portrait.jpg`, `[client to provide]`) - if the file is absent, the section must still render correctly as type-only. Do not substitute stock imagery.

**4. What we actually speak** - Job: the specialist proof. Names the income types plainly: incapacity payments, CSC pensions, DVA entitlements, DHOAS. See the DO NOT list about tax.

**5. Questions people actually ask** - Job: kill the objections before the form. Four FAQ items in a plain accordion (native `<details>`/`<summary>` - no JS library).

**6. How the Soldier On donation works** - Job: make the mechanic unmistakable. Three steps: see the ad → mention it when you enquire → $600 to Soldier On when your loan settles. **The wording of this section is compliance-sensitive - see DO NOT.**

**7. Testimonials** - Job: proof. **There are no veteran testimonials yet.** Build the section markup, leave it commented out with a clear `<!-- TESTIMONIALS: enable when real, attributable veteran testimonials are supplied -->` marker, and do not render an empty or placeholder-filled section. Never invent or paraphrase a testimonial.

**8. The form** (`id="enquire"`) + footer - Job: the conversion. Short form, then the legally required licensing footer.

---

## 4. Copy

Use this copy as written. It is approved and compliance-sensitive - do not rewrite, "punch up", or add adjectives. Australian English throughout.

**Hero**
- Eyebrow: `VETERANS HOME LOANS · AUSTRALIA-WIDE`
- H1: `The broker who speaks DVA.`
- Sub-line: `Veteran-owned and accredited with a DHOAS provider. Incapacity payments, CSC pensions and DVA entitlements are the day job here, not the exception.`
- CTA button: `Get Started`
- Trust line under the button: `Mention the Soldier On post when you enquire - $600 goes to Soldier On when your loan settles.`

**Section 2 - Your income isn't the problem**
- H2: `Your income isn't the problem. The translation is.`
- Intro: `Most lenders don't see many veteran files. That unfamiliarity can read as risk on a loan application, and it isn't.`
- Items:
  - `Incapacity payments explained as income, not as a question mark.`
  - `CSC pensions presented the way an assessor needs to see them.`
  - `DVA entitlements treated as what they are - income.`

**Section 3 - Who reads your file**
- H2: `Four careers. One loan file.`
- Items (each with a short gold rule above, matching the S2 ad): `Army veteran` / `Ex-Westpac, lender side` / `Bachelor of Laws` / `Built 17 apartments`
- Closing line: `Most brokers have seen one side of a loan. James O'Brien has seen all of them - as a veteran who has lived on ADF pay, as a Westpac lender who assessed files from the other side of the desk, as a law graduate who reads the fine print for sport, and as a developer who has built with borrowed money.`

**Section 4 - What we actually speak**
- H2: `Fluent in DVA.`
- Body: `Incapacity payments. CSC pensions. DHOAS. Veteran-owned, and accredited with a DHOAS provider - so the acronyms don't need decoding before the conversation starts.`

**Section 5 - Questions people actually ask** (four `<details>` items)
1. `Can I even get a loan on DVA income?` → `Plenty of veterans assume the answer is no before anyone has looked. The honest answer is that it depends on the file - which is why the first conversation is about reading the file. No promises before the numbers, and no assumptions either.`
2. `What does it cost me?` → `Nothing for the conversation. Brokers are generally paid by the lender on settlement, and anything that applies to your situation is explained in writing before you commit to anything.`
3. `How does the Soldier On donation work?` → `It's tied to the ad, not to every loan. If you saw the Soldier On post and mention it when you enquire, JGO Lending donates $600 to Soldier On when your loan settles. It comes from our side, never from your pocket, and nothing is priced into your loan to cover it.`
4. `What happens after I send this?` → `James calls you at a time that suits. You talk through what you're working with and whether the numbers stack up. If it isn't the right time, he'll say so.`

**Section 6 - The donation**
- H2: `Mention the post. $600 to Soldier On.`
- Three steps: `You saw the ad` → `Mention it when you enquire` → `$600 to Soldier On when your loan settles`
- Footnote: `From our side. Never from your pocket.`

**Section 8 - Form**
- H2: `Five short questions. About two minutes.`
- Intro: `Tell us where you're at and James will come back to you.`
- Submit button: `Get Started`
- Success state (replaces the form in place, does not navigate away): H3 `Thanks - that's the hard part done.` Body: `James will be in touch shortly. If you mentioned the Soldier On post, $600 goes to them when your loan settles.`
- Error state: `That didn't send. Try again, or email [EMAIL - client to provide].`

---

## 5. Brand tokens

Set these as CSS custom properties at the top of the file.

```css
:root{
  --jgo-black:      #0A0A0A;   /* page ground */
  --jgo-charcoal:   #282828;   /* radial gradient edge */
  --jgo-white:      #FFFFFF;   /* primary text */
  --jgo-gold-deep:  #9D722D;
  --jgo-gold-mid:   #D8BD79;   /* flat gold: rules, small accents, focus rings only */
  --jgo-gold-bright:#E4C377;
  --jgo-gold-light: #FAF7C8;
}
```

- **Page ground:** `radial-gradient(ellipse 120% 90% at 50% 42%, #282828 0%, #161616 55%, #0A0A0A 100%)`. White text on it is the default pair.
- **The gold gradient is the signature and it is non-negotiable:** `linear-gradient(180deg, #FAF7C8 0%, #E4C377 42%, #9D722D 100%)`, applied to display type via `background-clip:text` and to rules as a background. **Never use a flat yellow in its place.**
- **Fonts - match the ad creative exactly** so the page and the feed read as one thing. Self-host or load from a CDN: **Montserrat** 700/800/900 for headings and the CTA (this is the visual match to the JGO wordmark lettering), **Inter** 400/600 for body copy. Fallback stack: `-apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif`.
- **Logo:** the transparent white lockup, `assets/jgo-logo-white.png` `[client to supply the file - it is the "white logo.png" in the campaign assets]`. Max height 40px on mobile, 52px desktop. Never stretch, recolour, rotate, or add effects. If the file is missing, leave the space empty - **do not draw, recreate or substitute a logo.**
- **Tone in one line:** professional, straight-talking, service-background credibility - plain sentences, no hype, no exclamation marks.
- **Banned on this page:** flat yellow anywhere; em dashes (use a spaced hyphen); "unlock", "supercharge", "seamless", "journey", "hassle-free"; US spelling; US-style imagery; any invented insignia, medals or unit patches.

---

## 6. Conversion mechanics

### The form

`id="enquire"` on the section. Five required fields, one optional, plus the donation checkbox. Do not add fields.

| # | Field | Type | Required | Notes |
|---|---|---|---|---|
| 1 | Name | text | Yes | |
| 2 | Email | email | Yes | HTML5 validation |
| 3 | Phone | tel | Yes | Accept spaces and `+61`; do not over-validate |
| 4 | What are you looking to do? | select | Yes | Buy my first home / Buy my next home / Refinance / Invest / Not sure yet |
| 5 | Your service status | select | Yes | Ex-serving / Currently serving / Partner of a veteran / Prefer not to say |
| 6 | Anything we should know? | textarea | No | |
| 7 | `I saw the Soldier On post` | checkbox | No | **This is the donation trigger** - the digital equivalent of "mention it when you enquire". Label it exactly: `I saw the Soldier On post` |

**Do not collect** income figures, DVA payment amounts, medical details, date of birth, or any financial account information. The endpoint is email - sensitive financial and health data must not travel through it. Anything of that kind is discussed on the phone.

**Submits to:** a form-to-email service endpoint (Formspree, Web3Forms or equivalent). Put the endpoint in a single constant at the top of the script block:
```js
const FORM_ENDPOINT = "REPLACE_ME"; // [client to provide]
```
Submit via `fetch()` with `async`/`await`. **Do not navigate away.** On a successful response, replace the form with the inline success state. On failure, show the error state and leave the entered values intact so nothing is retyped.

Include a **honeypot** field (visually hidden, e.g. `name="company"`) and silently discard any submission where it is filled. No CAPTCHA.

### Meta Pixel and the conversion event

Put the Pixel ID in one constant so it can be swapped without hunting:
```js
const META_PIXEL_ID = "REPLACE_ME"; // [client to provide from Events Manager]
```

1. **Base pixel** in `<head>`, standard Meta snippet, including the `<noscript>` image fallback. `PageView` fires automatically on load. Nothing else fires on load.
2. **Form start** - a *custom* event on the first focus of any form field, fired once per page view only:
   `fbq('trackCustom', 'FormStart');`
   Guard it with a module-scope boolean (not storage) so repeated focus does not re-fire. This is the diagnostic that separates "nobody reaches the form" from "people start it and abandon".
3. **The conversion** - the standard `Lead` event, and **only after the endpoint returns a successful response**:
   ```js
   const eventId = (crypto.randomUUID && crypto.randomUUID()) || String(Date.now()) + Math.random();
   fbq('track', 'Lead', { content_name: 'veterans-enquiry' }, { eventID: eventId });
   ```
   - **Fire on success, never on button click.** A click-fired event counts failed submissions as leads and teaches the algorithm to find people who cannot submit forms.
   - The `eventID` exists so a Conversions API event can be deduplicated against this one later. It costs nothing now and cannot be retrofitted to leads already collected.
   - Fire it exactly once per successful submission - disable the submit button while the request is in flight.
4. **Never pass personal or financial data to the pixel.** No name, email, phone, service status, loan amount or income in any event parameter. Meta's business tools terms prohibit sending sensitive financial information, and this is a credit product. `content_name` is the only parameter needed.
5. No cookie-consent library, no consent gate. Australian traffic, and adding one is out of scope for this build.

### Verifying the tracking works

The builder must confirm, not assume:
1. Load the page with the Meta Pixel Helper browser extension - `PageView` fires once, no errors.
2. Focus a form field - `FormStart` appears once; focus another field - it does not fire again.
3. Submit the form with a real test entry - `Lead` fires **once**, after the success state appears.
4. Simulate a failed submit (temporarily point `FORM_ENDPOINT` at an invalid URL) - the error state appears and **no `Lead` event fires**. This is the check most builds skip and it is the one that matters.
5. Confirm all three events appear in Events Manager → Test Events.

---

## 7. Follow the existing pattern

There is no existing site to match - `jgolending.com.au` is currently a GoDaddy holding page. **The pattern to match is the ad creative**, and the finished frames are the reference: dark ground, heavy Montserrat display type, the gold gradient reserved for one emphasised phrase per section, gold hairline rules as separators, generous vertical spacing, no drop shadows, no glassmorphism, no gradients other than the ground and the gold. If a section looks like a generic SaaS template, it is wrong.

---

## 8. Do NOT

- **Do NOT write "every loan" or "every settlement" anywhere near the donation.** The donation is tied to the enquirer mentioning the ad. `$600 to Soldier On when you mention the post and your loan settles` is correct; `$600 to Soldier On on every loan` is a false claim and was killed at ad review for exactly this reason.
- **Do NOT state or imply the tax treatment of DVA payments, incapacity payments or CSC pensions.** Name the income types; say nothing about how they are taxed.
- **Do NOT add any interest rate, comparison rate, repayment figure or borrowing-capacity number.** Under ASIC RG 234 a rate cannot appear without its required comparison rate, and none of these figures have been supplied.
- **Do NOT write "guaranteed", "pre-approved", "approval guaranteed", "no credit check", or any promise about the outcome.** The approved formulation is `No promises before the numbers. No assumptions either.`
- **Do NOT imply Soldier On, DVA or the Department of Defence endorses, sponsors or is affiliated with JGO Lending.** State only what JGO does.
- **Do NOT use the Soldier On logo or any Defence/DVA insignia** until written permission is confirmed. Text reference only.
- **Do NOT invent testimonials, star ratings, review counts, client numbers, settlement figures or years of experience.** If a number is not in this brief, it does not go on the page.
- **Do NOT draw, redraw or approximate the JGO logo.** Use the supplied file or leave the space empty.
- **Do NOT touch the existing holding page, the domain's DNS, or any other file on the hosting.** This build is one new file in one new directory.
- **Do NOT add a framework, package manager, CSS library, icon library, analytics tool, chat widget or A/B testing script.** Meta Pixel and the form endpoint are the only external calls.
- **Do NOT fire the `Lead` event on click, on page load, or on the anchor jump to `#enquire`.**

---

## 9. Compliance footer (required - the page cannot go live without it)

Small type, muted, below the form. Fill the placeholders before publishing:

```
JGO Lending · James O'Brien
Credit Representative Number [CR NUMBER - client to provide]
of [AGGREGATOR LEGAL NAME - client to provide], Australian Credit Licence [ACL NUMBER - client to provide]

The information on this page is general in nature and does not take your objectives,
financial situation or needs into account. All applications are subject to lender
assessment, and approval is not guaranteed.

[Privacy Policy link - client to provide URL]
```

Render the placeholders visibly as written so nobody can publish accidentally without noticing them.

---

## 10. How to verify

Tick every one before reporting done:

- [ ] At 375px wide, the H1 and the `Get Started` button are both fully visible without scrolling
- [ ] The H1 reads exactly `The broker who speaks DVA.`
- [ ] `https://.../veterans#enquire` loads with the form visible and not obscured by any sticky element
- [ ] Every hero, section and FAQ string matches Section 4 of this brief character for character
- [ ] Submitting with a required field empty shows a validation message and does not send
- [ ] A valid submission replaces the form with the success state in place, without navigating away
- [ ] A failed submission shows the error state, keeps the typed values, and fires no `Lead` event
- [ ] Meta Pixel Helper shows `PageView` once on load, `FormStart` once on first field focus, `Lead` once after a successful submit - and nothing else
- [ ] No name, email, phone or service status appears in any pixel event payload (check the network tab)
- [ ] The strings "every loan", "guaranteed", "pre-approved" and "no credit check" appear nowhere in the file
- [ ] No interest rate or repayment figure appears anywhere in the file
- [ ] The licence footer renders with its placeholders clearly visible
- [ ] Page loads with zero console errors and zero 404s (missing `[client to provide]` image files degrade gracefully rather than showing broken-image icons)
- [ ] Total page weight under 500KB excluding fonts
- [ ] Keyboard-only: every form field and the submit button are reachable by Tab with a visible focus ring
- [ ] Colour contrast of body text on the dark ground is at least 4.5:1

---

## 11. Assets and answers still needed from the client

Flag these in the handover note; the page can be built without them but not published:

1. Meta Pixel ID (Events Manager)
2. Form endpoint URL, and the destination email address for the error state
3. Credit Representative number, aggregator legal name, aggregator ACL number
4. Privacy policy URL
5. `jgo-logo-white.png` (the transparent white lockup from the campaign assets)
6. `james-portrait.jpg` - optional, the section renders type-only without it
7. Written confirmation from Soldier On before any donation copy goes live
