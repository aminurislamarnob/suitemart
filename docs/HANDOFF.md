# Handoff — state of the work

Last updated: 2026-08-12, after finishing P2 batch A.

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
| **P2 batch C — site features** | Not started (6 blocks) ← **you are here** |
| **P3 — Design breadth** | 1 of ~15 style variations, 15 of ~100 patterns |
| **P4 — Integrations** | Not started (27) |
| **P5 — Hardening** | Not started |

**Green:** 230 PHPUnit tests passing / 1 skipped (the expected inverse guard),
106 Playwright tests passing / 1 skipped, phpcs clean, PHPStan level 5 clean,
ESLint and Stylelint clean — and CI genuinely runs the commerce suite rather
than skipping it.

46 blocks exist under `src/`. 18 templates, 6 parts, 24 patterns, 1 style
variation.

---

## Pick up here: P2 batch C

Batches A and B are complete. Batch B's twelve outside-written blocks were
audited and repaired; that audit found twenty defects, six visible to an end
user, on a tree where all seven verification commands passed. The gaps that let
them through are now covered by `tests/phpunit/test-design-tokens.php`,
`tests/phpunit/test-block-output.php` and an axe pass over the shop grid. **The
traps in `AGENTS.md` §5 are the condensed version — read them before writing
another block.**

**Batch C, still to build (6):** popup, floating-block, portfolio-grid (CSS grid
+ Interactivity filtering, no Isotope), post-carousel, back-to-top,
cookie-notice.

Build one per commit, each with its PHPUnit render test and at least one pattern,
each verified against the full command list in `AGENTS.md` §6.

### Three notes on blocks already built

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
