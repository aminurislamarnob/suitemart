# Working on Suitemart

Read this before writing any code. It is the standing contract for this
repository — conventions, hard rules, and the traps that have already cost real
debugging time. For *where the work currently stands and what to do next*, read
`docs/HANDOFF.md`.

Suitemart is a commercial multipurpose full-site-editing WordPress block theme
for WooCommerce. It targets the *capability surface* of the Woodmart theme, built
FSE-native rather than ported.

---

## 1. The one rule that is not negotiable

**Woodmart is a specification reference only. Never copy anything from it.**

A copy of Woodmart may be present at `../woodmart` and
`../../plugins/woodmart-core`. You may read it to understand *what a feature
does*. You may not copy its code, CSS, class names, markup structure, JavaScript,
icon fonts, demo images, or compiled assets, and you may not reproduce its visual
identity.

Why it matters: Woodmart's PHP is GPLv3, but its `woodmart-font*.woff2` icon
fonts, demo photography, and compiled CSS/JS fall under ThemeForest's split
licence, and its look is trade dress. Suitemart is sold commercially. Every line
of code, every stylesheet, every icon and every image here must be original or
separately and compatibly licensed.

Icons come from **Lucide** (MIT), via `npm run build:icons`. Fonts are
self-hosted OFL. Demo photography is Unsplash, development-only, never committed.

---

## 2. Architectural decisions already made

These were decided with the theme's owner and are **not open for
re-litigation**. If you believe one is wrong, say so and wait — do not quietly
work around it.

| # | Decision |
|---|---|
| 1 | Parity of *outcomes* with Woodmart, using FSE-native mechanisms |
| 3 | Everything ships in the theme — one repo, one zip. No companion plugin |
| 5 | Cart, checkout and my-account use **WooCommerce's own blocks**, styled. Never replaced |
| 6 | Styling is `block.json` `supports` + `theme.json`. **No custom CSS generation engine** |
| 7 | Dynamic `render.php` by default. Static `save` only for stable wrappers |
| 8 | Front-end JS is the **Interactivity API** only. No jQuery. Vendor libs limited to Swiper and PhotoSwipe |
| 10 | Woodmart is reference only (see above) |
| 12 | Options panel is ~40 behavioural flags, not a visual settings panel |
| 14 | Own `suitemart/navigation` family — core Navigation cannot host arbitrary submenu content |
| 15 | Use Woo's product blocks inside Suitemart templates; build custom blocks only for genuine gaps |
| 16 | Per-block `style` + one small global sheet. Budget **≤60KB CSS per page** |
| 17 | Plain JavaScript + Sass. **No TypeScript** |
| 18 | Inline SVG sprite from Lucide (MIT), coloured with `currentColor` |
| 19 | Floors: WP 6.7+, PHP 8.1+, WooCommerce 9.0+ |
| 21 | WCAG 2.1 AA, full RTL, translation-ready |
| 22 | No licence checking, no auto-updater |
| 23 | No page-builder support. FSE only |
| 26 | `suitemart/visitor-counter` ships simulating its number, for Woodmart parity. The legal exposure was put to the owner in full and accepted (2026-08-12). Keep it out of every template — `patterns/commerce-visitor-count.php` is its only route in, and the `suitemart_visitor_count` filter its only route to real data |

**Out of scope, do not build:** licence validation, auto-updates, white-label,
setup wizard, Elementor/WPBakery anything, a custom CSS engine, a custom
cart/checkout, jQuery, TypeScript.

---

## 3. Conventions

| Concern | Rule |
|---|---|
| Block names | `suitemart/<kebab-case>` |
| Text domain | `suitemart` — on every `__()`, `_x()`, `esc_html__()` |
| PHP functions | prefixed `suitemart_`; constants `SUITEMART_*` |
| PHP files | `declare( strict_types=1 );` after the docblock, then `defined( 'ABSPATH' ) || exit;` |
| PHP variables in `render.php` | prefix locals `$sm_` — `render.php` runs in a shared scope and phpcs enforces this |
| CSS classes | `.sm-<block>` root, `.sm-<block>__<element>`, `.is-*` / `.has-*` for state |
| CSS values | never a hard-coded colour, size or radius — always a preset (`var(--wp--preset--*)` / `var(--wp--custom--*)`). Stylelint bans hex colours in `style.scss` |
| Sass | **logical properties only** (`margin-inline-start`, `inset-inline-end`, `padding-block`). This *is* the RTL strategy — there is no RTL stylesheet |
| Breakpoints | 576 / 768 / 1024 / 1280, mobile-first: `@use "../_shared/breakpoints" as *;` then `@include bp(md)` |
| Interactivity stores | one namespace per feature: `suitemart/<feature>` |
| Escaping | escape **all** output in `render.php` |
| Accessibility | keyboard-operable, visible `:focus-visible`, WAI-ARIA APG patterns, `prefers-reduced-motion` disables animation |

Shared code lives in `src/_shared/`: `_breakpoints.scss`, `_mixins.scss`,
`price.js` (Store API money formatting), `product-list.js` (localStorage list
handling), `focus.js`, `off-canvas-lock.js`, `icons.js`, `Icon.js`.

Useful PHP helpers in `inc/blocks/helpers.php`:
`suitemart_get_icon( $name, $args )`, `suitemart_clamp_int( $value, $fallback,
$min, $max )`, `suitemart_enum( $value, $allowed, $fallback )`,
`suitemart_has_woocommerce()`, `suitemart_compare_limit()`,
`suitemart_block_image( $id, $url, $alt, $class, $size, $eager )` for blocks
that accept either an attachment id or a bare URL.

---

## 4. Anatomy of a block

One directory per block under `src/`. Adding the directory is all it takes —
`inc/blocks/register.php` discovers everything in `build/` automatically, and
nothing is listed by hand.

```
src/<block-name>/
├── block.json    always — apiVersion 3, the single source of truth
├── index.js      always — registerBlockType( metadata.name, { edit } ), no save
├── edit.js       always — useBlockProps() + InspectorControls
├── render.php    dynamic blocks — receives $attributes, $content, $block
├── style.scss    front-end CSS (referenced as "file:./style-index.css")
├── editor.scss   editor-only CSS
└── view.js       interactive blocks — an Interactivity API store module
```

**`src/compare-button/` is the canonical example.** Copy its shape.

A commerce block must also be listed in `suitemart_woocommerce_block_slugs()` in
`inc/blocks/register.php`, so it is skipped when WooCommerce is absent rather
than fataling inside `render.php`.

Every block ships in the same commit as **a PHPUnit render test** and **at least
one pattern that uses it**.

---

## 5. Traps that have already caused bugs here

These are not hypothetical. Each one cost debugging time in this repository.

**Interactivity directives are evaluated on the server too.** This is the single
biggest source of bugs in this codebase. An expression the server cannot resolve
does not leave the previous value in place — it **erases the element's text or
strips the attribute entirely**. A `data-wp-bind--aria-label` whose expression is
unresolved leaves a button with no accessible name. Every bound value must be
seeded in PHP, via `wp_interactivity_state()` or
`wp_interactivity_data_wp_context()`, *and* written as a literal fallback in the
markup. Three separate blocks shipped with erased output before this was
understood. Measured, not guessed: an unresolved `state.x` on
`data-wp-bind--aria-expanded` deletes an `aria-expanded="false"` written right
beside it, and `!state.x` evaluates `!null`, so the element is served `hidden`
for entirely the wrong reason.

**When a binding depends on an element's position among its siblings, declare
the derived state twice.** A getter like `state.isCurrentFrame` that compares
this element's index to the active one cannot be seeded as a plain value,
because the answer differs per element — which is why the trap above keeps
catching people here. The way out is a PHP closure that calls
`wp_interactivity_get_context()`: it runs once per element during directive
processing, with that element's own context in scope, so the server resolves the
binding exactly as the browser will. `src/view-360/` is the worked example, with
the same comparison written in `render.php` and in `view.js`. Yes, it is
duplicated logic; the alternative is a block that renders nothing until
hydration.

**A captured `getContext()` proxy goes stale outside its scope.** Reading it
back from a listener, timer or promise the store did not invoke returns the
value the page was *served* with, forever. Writes still land, which is what
makes it so hard to see: the state changes once and then appears frozen. A
back-to-top button appeared on the first scroll and never went away again, and
`view-360`'s auto-rotate would have advanced one frame and spun in place. Wrap
the callback in `withScope()` and call `getContext()` **inside** it. Test both
directions of anything that toggles — a spec that only scrolled down passed the
broken version.

**Never write to the DOM imperatively inside a hydrated tree.** `view-360`
originally moved an `is-current` class between frames from a `data-wp-watch`
callback. It worked in isolation and skipped frames at random in a browser:
Preact owns those attributes and restores what it last rendered on the next
update. Bind the attribute instead. A `data-wp-init` callback adding a class the
server never rendered (`is-enhanced`) is fine — the conflict is only over
attributes the framework is already managing.

**A `render.php` is `require`d inside an output buffer, so `return $content;`
returns nothing.** The file's return value is discarded and the buffer is what
reaches the page — which is empty, so the block silently disappears. `return
'';` reads as if it works only because empty is what those blocks mean. To pass
content through unchanged, `echo` it and then `return`.

**A function declared in `render.php` fatals on the second instance.** The file
is included once per rendered block, so the second one redeclares it. Shared
render helpers belong in `inc/blocks/helpers.php`.

**The server must never render visitor state.** Wishlist and comparison lists
live in `localStorage`, deliberately: no per-visitor cookie means pages stay
fully cacheable, which is what lets the P4 caching integrations work. So the
server renders every wishlist button as `aria-pressed="false"` and lets the
browser correct it. Rendering a saved state would show one visitor's wishlist to
everyone behind a full-page cache. There are tests asserting the served HTML
contains no product ids and that no cookie is set — do not "fix" them.

**`repeat()` in CSS grid needs an integer literal.** `repeat( var( --n ), 1fr )`
is invalid and silently drops the entire declaration, which reads as a layout
bug rather than the CSS error it is. Generate classes instead — see
`sm-wishlist-grid--cols-N`.

**The Store API route travels in a query parameter under plain permalinks.**
`?rest_route=/wc/store/v1/products`, url-encoded. So `/wp-json/` paths 404 and
Playwright path globs never match. Always build URLs with `rest_url()`, and
match routes in tests with a predicate:

```js
await page.route(
    ( url ) => decodeURIComponent( url.href ).includes( 'wc/store/v1/products' ),
    ( route ) => route.fulfill( { status: 500 } )
);
```

**Store API prices are minor units as strings.** The response carries
`currency_minor_unit`, `currency_prefix`, `currency_suffix`, `currency_symbol`
and the separators. Use `formatPrice()` from `src/_shared/price.js`. Do **not**
use `Intl.NumberFormat` — it applies the browser's idea of the currency rather
than the shop's settings. Note the prefix already contains the symbol;
concatenating both produced `$$19.99`.

**Report storage failures honestly.** With site data blocked, a `localStorage`
write fails silently. `toggleInList()` returns a `stored` flag for exactly this
reason: a caller that assumes success shows a filled heart for a wishlist that
will be empty on the next page load. Do not drop the flag.

**Avoid `GLOB_BRACE`** — undefined on the musl libc the Alpine container uses.
Use two `glob()` calls.

**`wp eval-file` runs through `eval()`**, where `declare( strict_types=1 )` is a
parse error. `tools/seed-demo-products.php` is the one file without it, and says
so in a comment.

**`suitemart_get_icon()` returns markup — it does not print it.** Four blocks
called it bare, so the icon silently vanished; one of them was a modal's close
button, which shipped as a visibly empty control. Every call site needs `echo`.
`tests/phpunit/test-block-output.php` now asserts an `<svg>` reaches the markup.

**Never hardcode a DOM id in a block.** A block appears once on the page you
tested and twelve times in a product grid. A hardcoded id gave four size guides
the same `id`, `aria-controls` and `aria-labelledby`, and one click opened all
four. Derive ids from `postId` context — `suitemart_size_guide_id()` shows the
shape, including how two sibling blocks with no shared ancestor agree on one.
Likewise keep per-instance state in **context**, not in `wp_interactivity_state()`:
global state is global to every instance on the page.

**`supports.interactivity` is what makes the server process directives.** Without
it the seeded state never reaches the markup, so `data-wp-bind--hidden` does not
apply and the element flashes visible until hydration. Two blocks shipped with
`data-wp-*` attributes and no such support.

**`get_woocommerce_currency_symbol()` returns an HTML entity** (`&#36;`, not
`$`). Seeded into Interactivity state and written out through `data-wp-text` —
which sets `textContent` — it renders literally as `&#36;19.99`. Decode it with
`html_entity_decode()`.

**Store API cart writes must be sequential.** They all mutate one session, so
concurrent `add-item` requests race and drop items; a guest with no session yet
gets a separate cart per request. `Promise.all` over a set of adds is a bug.

**Never render a nonce into block markup.** Behind a full-page cache it is served
stale to everyone and every request using it 403s. Read one off the Store API at
interaction time. There is a test asserting no `render.php` calls
`wp_create_nonce`.

**A `var()` fallback is not a safety net.** `var( --wp--preset--color--tertiary,
#b45309 )` hides the fact that the token does not exist *and* stops style
variations restyling the block, which is the point of decision 6. Reference
presets bare; `tests/phpunit/test-design-tokens.php` enforces both halves and
would have caught the nineteen invented tokens that reached `main`.

**WooCommerce's variation state is off limits.** The selected variation lives in
Woo's `woocommerce/products` Interactivity store, which is locked private behind
an unlock string that says in as many words that reading it will break on the
next release. Integrate through the add-to-cart form instead: classic and block
forms both carry one `attribute_<name>` field per attribute, because that is
what the cart consumes. `src/product-gallery/variations.js` is the worked
example. Related: `WC_Product_Variation::get_image_id()` falls back to the
parent's image, so ask for `get_image_id( 'edit' )` when you need to know
whether the variation has one of its own.

**A fixed-position block still gets the constrained layout's `max-width`.**
Every direct child of the content area is capped at `contentSize` and centred
with auto margins, and `position: fixed` does not exempt it: the cookie notice
rendered as a 720px bar hovering in the middle of the viewport with 516px of
margin on each side. Reset `max-width` and `margin` in the block's own sheet,
and double the class (`.sm-x.sm-x`) — the layout rule's specificity is a single
class too, so which stylesheet loads last would otherwise decide it.

**A preset slug is not the property name.** Core kebab-cases a slug before it
becomes CSS, and that splits a digit from the letter after it: the size declared
as `2xl` is emitted as `--wp--preset--font-size--2-xl`, and its utility class is
`has-2-xl-font-size`. Spell it the obvious way and nothing errors — the
declaration is simply dropped. `theme.json` asked for `--3xl` on `elements.h1`
and eleven files wrote `has-3xl-font-size`, so **every heading in the theme
rendered at body size**, on every page, for weeks. Slugs stay as they are;
references go through core's own `_wp_to_kebab_case()`, which is what
`tests/phpunit/test-design-tokens.php` now does — and it checks `theme.json` and
the hand-written classes in `patterns/`, `templates/` and `parts/` as well as
the stylesheets.

**A block style has to be registered before its class does anything.**
`is-style-none` was written into the header and footer parts and five patterns
without a `register_block_style()` call anywhere, so every link list in the mega
panel and the footer rendered indented and bulleted. Same shape as the trap
above: an unregistered class is not an error, it is just a class.

**`block.json` will name a stylesheet the build never made.** wp-scripts
compiles `style.scss` only when something imports it, and the import belongs in
the block's `index.js`. Miss it and `"style": "file:./style-index.css"` still
registers — against a URL that 404s — so the block renders with user-agent
styling and nothing warns. Quick view, the product gallery and
frequently-bought-together shipped that way; on a white page an unstyled
`<button>` looks near enough to a designed one that it survived review, and it
only became obvious under a dark style variation. `tests/phpunit/test-block-assets.php`
now checks both directions: every `file:` reference resolves, and every sheet in
`src/` is declared.

**`<a>` and `<button>` disagree about `box-sizing`, and a shared `min-width`
then means two sizes.** The theme has no global border-box reset, so a link
computes as `content-box` and a button as `border-box`. `.sm-share__link` set
`min-width: 40px` on both: the four share links rendered at 59px and the copy
button at 40, so the row read as four circles with a smaller odd one out. Any
rule sizing a mixed set of links and buttons has to state `box-sizing` itself.

**A block that was drawn as an overlay cannot nest inside a Woo block.** A
product card is assembled by `woocommerce/product-template`, and nothing can go
*inside* `woocommerce/product-image` — so `suitemart/product-labels` could only
ever be its sibling. In flow it pushed the image down by however many labels a
product had, and a row of four cards ended up with four different image tops.
The fix is the block positioning itself against Woo's card through `:has()`,
scoped to the card so the same block still sits in flow in a product summary.
Same technique as the pinned-block collisions above, same reason: the rule lives
in the yielding block's own sheet and costs nothing elsewhere.

**`woocommerce/product-image` draws its own sale badge.** Pair it with
`suitemart/product-labels`, which draws one too, and the card shows SALE twice —
once per corner. Four patterns shipped like that. Set `"showSaleBadge":false` on
the image wherever our labels are present.

**The pattern list is cached, so a new pattern file does not exist yet.**
`WP_Theme::get_block_patterns()` caches the header scan, and it survives
`wp cache flush` and `wp transient delete --all`. A newly written pattern is
absent from `WP_Block_Patterns_Registry` until `wp_clean_themes_cache()` runs —
which reads as "my pattern is broken" when nothing is wrong with it. PHPUnit
never sees this because the pattern tests glob the directory directly.

**Images must be constrained globally.** `src/global.scss` caps `img` at
`max-width: 100%`. Without it, WooCommerce's full-size gallery image overflowed
its column, sat invisibly on top of the summary column, and swallowed every
click meant for add-to-cart. The page looked fine and simply did not respond.

---

## 6. Environment and verification

```bash
npm install && composer install
npm run build
npm run env:start
```

wp-env installs themes but does **not** reliably activate one;
`tests/e2e/global-setup.js` activates Suitemart itself for this reason. If
`wp-env start` exits 0 with only the MySQL container (seen with OrbStack), the
manual recovery steps are in `tests/README.md`.

Seed a browsable catalogue — 20 products, five categories, sale prices,
low-stock and out-of-stock states:

```bash
npx wp-env run cli --env-cwd=wp-content/themes/suitemart \
    wp eval-file tools/seed-demo-products.php
```

**Every one of these must pass before a commit.** CI runs them across WP stable
and WP nightly and is currently green — keep it that way.

```bash
npm run build          # clean
npm run lint:js
npm run lint:css
npm run lint:php       # phpcs, WordPress-Coding-Standards
npm run analyze:php    # phpstan level 5
npm run test:php       # PHPUnit
npm run test:e2e       # Playwright + axe
```

Two things about the test suites that are easy to get wrong:

- The PHPUnit bootstrap loads WooCommerce explicitly. The WP test library loads
  no plugins, so without it every commerce test *skips* — and a run that skips
  its entire commerce surface reports green while testing nothing. If you see
  skips, that is a failure, not a pass. Exactly one skip is expected: the
  inverse-condition guard in `test-block-render.php`, which only runs when
  WooCommerce is *absent*.
- Never hardcode a product or page id in an e2e spec. Read the product id off
  the block's `dataset.wpContext`; the fixture page URLs come from environment
  variables exported by `global-setup.js`.
- **Assert output, not shape.** `toBeVisible()` and `not.toBeEmpty()` pass on a
  total that reads `&#36;557.00` and on a button with no icon in it. Twelve
  blocks reached `main` green while carrying six user-visible defects, because
  nothing checked a rendered icon, a formatted price, a design token, or a
  second instance of a block on one page. If a defect would survive your test,
  the test is not testing.
- Nothing in the fixture setup may reach the network. A fixture downloaded from
  a remote host fails the suite whenever that host is unreachable, and a
  download guarded with an `if` turns into a spec asserting against an empty
  element. The gallery images are committed under `tests/e2e/fixtures/`.
