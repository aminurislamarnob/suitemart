# Handoff — state of the work

Last updated: 2026-08-13, after P3's content and CTA patterns.

Read `AGENTS.md` first — it holds the standing rules. This file is a snapshot:
what is built, what is verified, and what to pick up next. **Update it when you
finish a batch**, or the next person inherits a lie.

The full plan lives in GitHub issue
[#1](https://github.com/aminurislamarnob/suitemart/issues/1).

---

## Where things stand

| Phase | State |
|---|---|
| **P1 — Foundation** | Complete, with one item outstanding (CSS budget, below) |
| **P2 batch A — content blocks** | All 18 built |
| **P2 batch B — WooCommerce gaps** | All 15 built; audited and repaired |
| **P2 batch C — site features** | All 6 built |
| **P3 — Design breadth** | All 15 style variations built; 89 of ~100 patterns ← **you are here** |
| **P4 — Integrations** | Not started (27) |
| **P5 — Hardening** | Not started |

**Green:** 459 PHPUnit tests passing / 1 skipped (the expected inverse guard),
144 Playwright tests passing / 1 skipped, phpcs clean, PHPStan level 5 clean,
ESLint and Stylelint clean — and CI genuinely runs the commerce suite rather
than skipping it.

52 blocks exist under `src/`. 18 templates, 6 parts, 89 patterns, 15 style
variations — and **every block now appears in at least one pattern**, which
`tests/phpunit/test-pattern-coverage.php` holds.

---

## Pick up here: P3, design breadth

Batches A and B are complete. Batch B's twelve outside-written blocks were
audited and repaired; that audit found twenty defects, six visible to an end
user, on a tree where all seven verification commands passed. The gaps that let
them through are now covered by `tests/phpunit/test-design-tokens.php`,
`tests/phpunit/test-block-output.php` and an axe pass over the shop grid. **The
traps in `AGENTS.md` §5 are the condensed version — read them before writing
another block.**

All three P2 batches are done: 18 content blocks, 15 WooCommerce blocks, and
batch C's six site features — back-to-top, cookie-notice, floating-block, popup,
post-carousel, portfolio-grid.

**Next is the rest of P3**: the 10 full-page starter patterns. Every section
category is now filled — headers, heroes, commerce, content, CTA, footers — so
the starters should be **assembled from the section patterns rather than written
from scratch**: paste the section markup in, adjust the copy so a page reads as
one voice, and delete what does not earn its place. A starter that duplicates a
section's markup rather than reusing it is a second copy to keep in step. Keep
building one thing per commit, each with its PHPUnit test and at least one
pattern, each verified against the full command list in `AGENTS.md` §6 — and
each checked in a real browser before its Playwright spec is written. Every bug
this session that mattered was invisible to PHP.

### The style variations

All fifteen are in `styles/`, numbered `01-midnight` through `15-circuit`. Each
one replaces the palette, the scrim and overlay, the four radii, and the
typographic voice (body family and leading, heading family, weight, tracking,
and the h6 label treatment) — and nothing else.
`tests/phpunit/test-style-variations.php` holds that line: a variation may
touch `settings.color.palette` and `settings.custom.{color,radius}`, must
declare all seventeen colour slugs in order, and may invent none. A slug it
drops is not a fallback — every block names its colours through
`var( --wp--preset--color--<slug> )`, so the declaration is dropped and that one
block renders unstyled in that one variation.

**P3's acceptance criterion — every variation passes WCAG AA — is met by that
test, not by axe.** Axe can only measure whichever variation is active, so
covering fifteen through the browser means fifteen runs of the whole suite. The
test instead computes the contrast ratio for each foreground/background pair the
theme's own stylesheets actually put together, read off `src/**/*.scss`: text at
4.5:1, and the controls that carry no text — icon-only buttons, carousel
pagination bullets — at 1.4.11's 3:1. Add a pair to those lists whenever a block
introduces one; the two defaults that failed the first run (`neutral-500` on a
card, and bullets drawn in `neutral-400`) were both found this way.

The neutral ramp in every variation is mixed from `base` toward `contrast` at
fixed proportions, which is what keeps `neutral-600` readable on a card in all
fifteen. If you hand-write a new variation, run the test before anything else.

Two are dark (`01-midnight`, `05-lumiere`), and they are the ones worth opening
a browser for: a rule that hardcodes light-on-light survives every light
variation. Applying one is how the three blocks with unloaded stylesheets were
found.

### The header, hero and footer patterns

Six of each, named `header-*`, `hero-*`, `footer-*`. The header and footer ones
carry `Block Types: core/template-part/header|footer`, which is what puts them
in the list the Site Editor offers when you edit a part; a pattern without it is
only reachable from the inserter. Four of the eighteen open with a WooCommerce
guard, because the cart and account blocks are Woo's and a pattern naming an
unregistered block inserts as an error rather than as a header.

Two things learned writing them, both worth repeating in the rest of P3:

- **No pattern ships a photograph.** Suitemart is sold commercially, so a stock
  image would have to be licensed for redistribution and every buyer would
  inherit the same picture. Image-led patterns use a cover or a banner with no
  media and say so in the placeholder copy. That is also why
  `suitemart/banner` now draws a dark ground when it has no image: the content
  of a banner is light text over a scrim, and the scrim over nothing is a pale
  grey the text vanishes into — in the inserter preview and on the page.
- **A countdown needs a date, and a date written into a file is already past.**
  `header-announcement` and `hero-offer-countdown` compute one with
  `wp_date( …, strtotime( '+7 days' ) )`, which is evaluated when the pattern is
  inserted and stored as an ordinary value from then on.

### The commerce section patterns

Twenty-two `commerce-*` patterns, sixteen of them new. Most are a
`woocommerce/product-collection` with a `collection` attribute —
`new-arrivals`, `best-sellers`, `on-sale`, `top-rated`, `related` — rather than
a hand-maintained query, so the section keeps meaning what its heading says as
the catalogue changes. All but `checkout-trust` open with the WooCommerce guard.

The slice existed to close a specific gap: **five blocks had reached `main` with
no pattern at all** — `wishlist-button`, `wishlist-grid`, `compare-button`,
`compare-table`, `back-to-top`. Four of those are two whole features a buyer
would have had to already know about to find. `test-pattern-coverage.php` now
fails the build if any block loses its last pattern, and also checks that every
`suitemart/*` category a pattern names is one `inc/patterns.php` registers.

Three things worth carrying forward:

- **`woocommerce/product-filter-removable-chips` cannot go in a pattern.** Its
  render reads a `$classes` it only assigns when the saved inner markup carries
  `wc-block-product-filter-removable-chips`, so hand-written markup raises
  "Undefined variable $classes" from WooCommerce itself and takes the whole page
  with it. `commerce-shop-filters` leaves it out and says why. Add it from the
  editor, where Woo writes its own markup.
- **Woo's filters render almost nothing on this demo catalogue**, and that is
  correct: `product-filter-active` and `-rating` mark themselves hidden with no
  active filters and no reviews. The shop archive's sidebar looks empty as a
  result. It is not broken — check before "fixing" it.
- **A tinted or bordered group needs horizontal padding**, not just vertical.
  Three patterns shipped with content sitting hard against the edge of their own
  panel, because a wide group's background is exactly the content width.

### The content and CTA patterns

Sixteen `content-*` and nine `cta-*`, bringing the library to 89. They lean on
core where core is enough — `core/details` for the FAQ so the browser owns the
open state and find-in-page reaches closed answers, `core/table` with real
`<th scope>` for the comparison table, two `core/query` blocks with
`inherit: false` for the blog sections so a "latest three" band says the same
thing wherever it is dropped.

Four defects came out of building them, and **three were in code that had
already shipped**:

- **`post-content` was not `align: full`,** so no pattern placed in page content
  could bleed to the edge of the screen — a full-width hero was capped at the
  720px column while the same markup in a header part spanned the viewport. Five
  templates fixed.
- **`suitemart/off-canvas` inherited its parent's width.** Fixed positioning does
  not exempt a block from the constrained layout's `max-width` and auto margins,
  so the drawer's scrim covered a stripe down the middle of the page and the
  panel floated in it. Only visible outside a header part, which is the only
  place it had ever been used.
- **Six patterns drew a four-sided box where they asked for one rule.** A
  per-side border width beside a block-wide `borderColor`; the colour brings
  `border-style: solid` on all four sides and the unset widths default to
  `medium`. `test-pattern-coverage.php` now fails the build on that combination.
- **`is-style-plain` on `core/quote` styles nothing** — core registers it in
  JavaScript only. Same trap as `is-style-none`, second time. The press quote is
  a `core/pullquote` now.

All four are written up in `AGENTS.md` §5.

Two smaller things worth carrying: an info box's `title` renders as a *heading*,
so a list of six of them puts six entries in the document outline — use
`description` for anything that is not really a heading. And the off-canvas panel
stacks its children with no block gap, so a run of blocks inside it needs a group
of its own to separate them.

### Notes on blocks already built

`suitemart/lightbox` (batch A) is the theme's PhotoSwipe integration, and the
answer to product-gallery zoom is to reuse its store rather than to write a
second one. That wiring is **not done**: the gallery has no zoom yet.

`suitemart/product-gallery` follows the selected variation and holds
`templates/single-product.html`.

`suitemart/visitor-counter` **ships as it is, by the owner's decision on
2026-08-12.** It invents its number unless a real source is connected through
the `suitemart_visitor_count` filter, which matches Woodmart's equivalent block
and decision 1's parity goal. The risks were put to the owner in full and
accepted: fabricated social proof is a prohibited commercial practice under the
EU Unfair Commercial Practices Directive as amended, is named in the UK's DMCC
Act 2024, and falls under the FTC's rule on deceptive endorsements — exposure
that lands on the merchant using the theme.

Do not re-open this, and do not quietly soften it either. The containment it has
is deliberate and is the thing to preserve: it appears in **no template**, only
in `patterns/commerce-visitor-count.php`, whose description states what the
block does. Leave that pattern the only route in. Worth adding to the readme at
P5: what the block does, and how to wire up a real source.

`suitemart/cookie-notice` records a choice and announces it — through the
`suitemart-cookie-consent` event and `data-sm-consent` on `<html>` — and blocks
nothing itself, because a theme block cannot. That limit is stated in the block
description, the inspector, the render docblock and the pattern description, and
all four should stay: a banner that lets a merchant believe they are compliant
when nothing behind it is wired up is worse than no banner. Accept and Decline
are drawn identically on purpose, and `tests/e2e/cookie-notice.spec.js` measures
it rather than trusting the class list. Like the visitor counter, it is in no
template; `patterns/cta-cookie-notice.php` is its only route in.

`suitemart/floating-block` and the two blocks above all pin themselves to the
bottom of the viewport, and they collided: the back-to-top button sat on top of
a corner panel's close button and of the cookie bar's Accept. The rules that
resolve it live in the sheet of the block that yields — `:has()` in
`src/floating-block/style.scss` and `src/cookie-notice/style.scss` — so they
cost nothing on pages without those blocks. **Anything else pinned to a screen
edge has to be checked against them.** The popup is the exception and needs no
such rule: `showModal()` paints it in the top layer, above everything.

`suitemart/popup` is a `<dialog>` opened with `showModal()`, and that is the
whole reason it is short — focus trapping, the inert page behind, Escape, focus
returning, and being above every z-index on the page all come from the browser.
Do not replace it with a div and a hand-written focus trap; `tests/e2e/popup.spec.js`
asserts each of those behaviours precisely so that swap cannot pass quietly.

`suitemart/post-carousel` and `suitemart/slider` share one Swiper configuration,
in `src/_shared/carousel.js`. Change it there, not in one of the two view
modules. Both are built as divs rather than lists on purpose: Swiper's a11y
module puts `role="group"` on every slide, and a `role="list"` may hold only
list items — axe calls that combination critical, and it is right.

`suitemart/portfolio-grid` filters by hiding projects that are already in the
page — no Isotope, no second request (decision 8), and the filters simply do
not appear without JavaScript, leaving the whole grid shown. Its visibility and
pressed-state bindings are derived from context, so they are declared **twice**:
PHP closures in `render.php` and getters in `view.js`. Change one and you must
change the other. The click handler sits on the filter bar rather than on each
button, because the buttons carry a context of their own and a write from
inside one would land there instead of on the grid.

### The pattern to copy

`src/compare-button/` (simple, interactive) and `src/compare-table/` (renders a
list fetched client-side) are the two canonical shapes. Between them they
demonstrate everything batch B needs: server-seeded Interactivity state,
`usesContext: [ "postId" ]` with a bail-out when the post is not a product,
`localStorage` via `src/_shared/product-list.js`, Store API fetching, and
`formatPrice()` from `src/_shared/price.js`.

Their tests — `tests/phpunit/test-compare.php` and
`tests/e2e/compare-table.spec.js` — are the shape to copy too.

---

## Known outstanding issue

**CSS budget.** The home page ships 64.3KB against a ≤60KB target (decision 16).
Suitemart's own share is only 10.8KB; the overage is WordPress's `global-styles`
(23KB) and core social-links (12KB). This needs trimming before P5 signs off,
but it is not a blocker for building blocks. Do not solve it by inlining or by
adding a build-time CSS optimiser — the fix is narrowing what `theme.json`
emits.

---

## After batch B

- **P2 batch C** (6): popup, floating-block, portfolio-grid (CSS grid +
  Interactivity filtering, no Isotope), post-carousel, back-to-top,
  cookie-notice.
- **P3** — 15 style variations overriding *only* palette, typography and radius.
  The colour slugs in `theme.json` never change; that is what makes variations
  cheap. Acceptance is an automated axe contrast pass per variation. Then ~100
  patterns across `suitemart/hero|commerce|content|cta|footer|header`.
- **P4** — integrations, in a fixed order by buyer impact: WPML/Polylang first,
  then SEO plugins, then caching, then the Woo ecosystem, then multivendor, then
  the tail. Acceptance per integration: activating the plugin causes no fatal or
  notice, and its primary flow works on Suitemart templates.
- **P5** — options screen (~40 behavioural flags in one `suitemart_options`
  array), system status tab, full a11y audit, RTL sweep, `makepot` in CI,
  performance budget, light WXR importer, docs.
