# JGO Lending - veterans landing page

The Meta-traffic landing page for the JGO Lending veterans home loan campaign.
Serves at `https://jgolending.com.au/veterans`.

Built from [`brief-veterans-landing-page.md`](brief-veterans-landing-page.md).
The copy in that brief is approved and compliance-sensitive: it is transcribed
character for character and must not be reworded without a fresh compliance
check.

## Layout

```
veterans/index.html    The page. One self-contained file, all CSS and JS inline.
veterans/send.php      Server-side form handler. Relays to the Resend API.
veterans/assets/       Client-supplied images. Empty until they are provided.
jgo-config.php         Secrets and mail config. NOT IN THIS REPO. See below.
```

There is no build step, no package manager and no framework. The page is
uploaded as-is.

## Deploying

Manual upload to the GoDaddy hosting for `jgolending.com.au`.

```
/home/<account>/jgo-config.php                     <- ABOVE the web root
/home/<account>/public_html/veterans/index.html
/home/<account>/public_html/veterans/send.php
```

`jgo-config.php` must sit outside `public_html` so it can never be served as a
file. `send.php` looks one level above the web root first and falls back to
`public_html` if the host does not allow files above it.

Do not touch the existing holding page, the DNS, or any other file on the
hosting.

## Configuration

`jgo-config.php` is deliberately absent from this repo and is listed in
`.gitignore`. It holds the Resend API key and must never be committed. Create
it on the server with:

```php
<?php
return array(
    'resend_api_key'      => 're_xxxxxxxxxxxx',
    'from'                => 'JGO Lending <enquiries@jgolending.com.au>',
    'to'                  => 'enquiries@jgolending.com.au',
    'rate_limit_per_hour' => 5,
);
```

The `from` domain must be verified in the Resend dashboard first, or every
send fails.

The Meta Pixel ID is in `veterans/index.html` in two places: the
`META_PIXEL_ID` constant and the `<noscript>` fallback URL. Pixel IDs are
public by nature and are safe in this repo.

## Tracking

| Event | When it fires |
|---|---|
| `PageView` | On load, once. |
| `FormStart` | Custom event, on first focus of any form field, once per page view. |
| `Lead` | Standard event, **only** after the endpoint returns a successful response. |

`Lead` carries an `eventID` so a Conversions API event can be deduplicated
against it later. It must never be moved to a click handler: a click-fired
event counts failed submissions as leads and trains the algorithm to find
people who cannot submit forms.

No name, email, phone or service status is ever passed to the pixel.
`content_name` is the only parameter.

## Before it can go live

- [ ] Credit Representative number (footer still shows a placeholder)
- [ ] Privacy policy URL
- [ ] Destination email for the form and the page's error state
- [ ] `assets/jgo-logo-white.png` - the transparent white lockup
- [ ] `assets/james-portrait.jpg` - optional, the section renders type-only without it
- [ ] Written confirmation from Soldier On before any donation copy goes live

Both image tags ship commented out with uncomment markers, so the page loads
with no broken images and no 404s while the files are outstanding. Do not
substitute stock imagery and do not recreate the logo.

## Verifying a change

Re-check these after touching the page:

- At 375px the H1 and the `Get Started` button are both above the fold
- `/veterans#enquire` lands with the form visible
- Every string still matches the brief character for character
- A failed submission shows the error state, keeps the typed values, and fires
  no `Lead` event
- No interest rate, comparison rate or repayment figure anywhere in the file
- The licence footer renders with its remaining placeholders visible
