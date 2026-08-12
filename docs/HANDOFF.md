# Handoff — state of the work

Last updated: 2026-08-12, at commit `33342ff`.

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
| **P2 batch A — content blocks** | 13 of 18 |
| **P2 batch B — WooCommerce gaps** | 9 of 15 ← **you are here** |
| **P2 batch C — site features** | Not started (6 blocks) |
| **P3 — Design breadth** | 1 of ~15 style variations, 15 of ~100 patterns |
| **P4 — Integrations** | Not started (27) |
| **P5 — Hardening** | Not started |

**Green:** 152 PHPUnit tests passing, 70 Playwright tests
passing / 1 skipped, phpcs clean, PHPStan level 5 clean, ESLint and Stylelint
clean. CI passes on WP latest *and* WP nightly.

35 blocks exist under `src/`. 18 templates, 6 parts, 15 patterns, 1 style
variation.

---

## Pick up here: finish P2 batch B

Five blocks remain. Every slug is **already declared** in
`suitemart_woocommerce_block_slugs()` in `inc/blocks/register.php` — create the
directory under `src/` and registration is automatic.

| Block | Notes |
|---|---|
| ~~`product-labels`~~ | Done |
| ~~`stock-progress-bar`~~ | Done |
| ~~`sold-counter`~~ | Done |
| ~~`estimated-delivery`~~ | Done |
| ~~`product-countdown`~~ | Done |
| ~~`visitor-counter`~~ | Done |
| `size-guide` + `size-guide-button` | Modal pair. Reuse the off-canvas focus handling in `src/_shared/focus.js` |
| `quick-view-button` | Modal rendering a product from the Store API. **The most substantial one** — do it when you have a clear run |
| `fbt-products` | "Frequently bought together". Needs a cart-add for multiple products |
| `product-gallery` | Thumbnails / vertical / grid variants. Largest surface; leave for last |

Build them one per commit, each with its PHPUnit render test and at least one
pattern, each verified against the full command list in `AGENTS.md` §6.

**Also still open in batch A:** `hotspots` + `hotspot`, `compare-images`,
`view-360`, and `lightbox` (PhotoSwipe is already installed, MIT-licensed, not
yet imported).

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
