# Testing Suitemart

Two suites, both run against a real WordPress install rather than mocks:

| Suite | What it protects | Command |
| --- | --- | --- |
| PHPUnit (`tests/phpunit`) | Render callbacks never fatal and never emit unescaped attributes; templates, parts and `theme.json` agree with each other | `npm run test:php` |
| Playwright (`tests/e2e`) | Every template loads clean; the mega menu is fully keyboard-operable and passes axe | `npm run test:e2e` |

Suitemart's blocks all render dynamically, so a malformed attribute reaches PHP
instead of being caught at save time. That is why the PHPUnit suite renders every
block against deliberately hostile attributes — wrong types, `<script>` payloads,
`javascript:` URLs — rather than only the happy path.

## Getting the environment up

```bash
npm install
composer install
npm run build
npm run env:start
```

### If `wp-env start` finishes silently without creating containers

This has been observed with OrbStack: `wp-env` prints nothing, exits `0`, and only
the MySQL container appears. The WordPress images are never built and the PHPUnit
library is never downloaded. Complete the setup manually:

```bash
WP_ENV_HOME="${WP_ENV_HOME:-$HOME/.wp-env}"
ENV_DIR="$WP_ENV_HOME/$(ls -t "$WP_ENV_HOME" | head -1)"

# 1. Build and start the containers wp-env skipped.
cd "$ENV_DIR"
docker compose build wordpress tests-wordpress cli tests-cli
docker compose up -d

# 2. Install WordPress in both environments.
docker compose run --rm -u 33 cli wp core install \
  --url=localhost:8888 --title=Suitemart --admin_user=admin \
  --admin_password=password --admin_email=admin@example.test --skip-email
docker compose run --rm -u 33 tests-cli wp core install \
  --url=localhost:8889 --title=Suitemart --admin_user=admin \
  --admin_password=password --admin_email=admin@example.test --skip-email

# 3. Activate the theme and WooCommerce in both.
for env in cli tests-cli; do
  docker compose run --rm -u 33 $env sh -c \
    'wp theme activate suitemart && wp plugin install woocommerce --activate'
done
```

Then install the WordPress test library, which the PHPUnit bootstrap needs. Match
the branch to the WordPress version the tests environment reports
(`docker compose run --rm -u 33 tests-cli wp core version`):

```bash
git clone --depth 1 --branch 6.9 --filter=blob:none --sparse \
  https://github.com/WordPress/wordpress-develop.git /tmp/wp-develop
git -C /tmp/wp-develop sparse-checkout set tests/phpunit

for d in WordPress-PHPUnit tests-WordPress-PHPUnit; do
  rsync -a /tmp/wp-develop/tests/phpunit/ "$ENV_DIR/$d/tests/phpunit/"
done
```

Finally write `wp-tests-config.php` **inside** `tests/phpunit/` — only that
directory is mounted into the container, so the usual repo-root location is not
visible from inside:

```php
<?php
define( 'ABSPATH', '/var/www/html/' );
define( 'DB_NAME', 'tests-wordpress' );   // 'wordpress' for the dev environment
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'password' );
define( 'DB_HOST', 'tests-mysql' );       // 'mysql' for the dev environment
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );
$table_prefix = 'wptests_';
define( 'WP_TESTS_DOMAIN', 'localhost' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );
define( 'WP_PHP_BINARY', 'php' );
define( 'WP_DEFAULT_THEME', 'default' );
```

Run the suite, pointing the bootstrap at that file:

```bash
cd "$ENV_DIR"
docker compose run --rm -u 33 \
  -e WP_TESTS_CONFIG_FILE_PATH=/wordpress-phpunit/wp-tests-config.php \
  -w /var/www/html/wp-content/themes/suitemart \
  tests-cli vendor/bin/phpunit
```

### The e2e fixture page

`blocks-a11y.spec.js` exercises the interactive blocks against a real page.
Create it once per environment:

```bash
cd "$ENV_DIR"
docker compose run --rm -u 33 -w /var/www/html/wp-content/themes/suitemart cli \
  wp post create tests/e2e/fixtures/blocks-page.html \
    --post_type=page --post_title="Suitemart block test" \
    --post_name=suitemart-block-test --post_status=publish --porcelain
```

The spec loads it by id (`/?page_id=10`). If your install assigns a different
id, update the `PAGE` constant at the top of the spec.

## Notes for anyone extending the suites

- **PHPUnit is pinned to 9.6 on purpose.** PHPUnit 10+ requires PHP 8.2, and the
  theme supports PHP 8.1 (decision 19). `composer.json` also pins
  `config.platform.php` to 8.1 so dependency resolution matches the floor rather
  than whatever PHP the developer happens to run.
- **Avoid `GLOB_BRACE`.** It is undefined on musl libc, which the Alpine-based
  container uses. Use two `glob()` calls instead.
- **Wait for animations before measuring colour.** A panel mid-fade is measured
  as its colour blended with the background, which fails a contrast check that
  the settled state passes. `nav-a11y.spec.js` has a `settle()` helper for this.
- **Scope landmark locators to `.wp-block-template-part`.** A bare `footer`
  locator also matches WooCommerce's filter-overlay footer, which is hidden by
  design.
