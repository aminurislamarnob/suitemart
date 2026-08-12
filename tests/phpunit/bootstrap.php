<?php
/**
 * PHPUnit bootstrap.
 *
 * Loads the WordPress test library and activates Suitemart before the suite runs,
 * so tests exercise the theme exactly as a site would.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! is_string( $_tests_dir ) || '' === $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	fwrite(
		STDERR,
		"Could not find the WordPress test library at {$_tests_dir}.\n" .
		"Run the suite through wp-env, which provides it:\n\n" .
		"  npm run env:start\n" .
		"  npm run test:php\n\n"
	);
	exit( 1 );
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Switches to Suitemart before WordPress finishes loading.
 *
 * @return void
 */
function suitemart_register_theme_for_tests(): void {
	switch_theme( 'suitemart' );
}
tests_add_filter( 'muplugins_loaded', 'suitemart_register_theme_for_tests' );

/**
 * Locates WooCommerce's entry file, whatever its directory is called.
 *
 * The directory is not called `woocommerce/` everywhere. wp-env names a plugin
 * after the source it came from, so the `woocommerce.latest-stable.zip` URL in
 * `.wp-env.json` installs to `woocommerce.latest-stable/`. Looking only for the
 * conventional name found nothing on CI, which is why every commerce test there
 * reported as a skip from the first commit onwards while the build stayed green
 * — 113 tests and 12 skips against 125 run locally, where a manual
 * `wp plugin install` had happened to leave a copy under the expected name.
 *
 * Matching on the plugin header instead covers both, and does not care what the
 * next environment decides to call the directory. Do not "fix" this by
 * installing a second copy: with one already present under another name, that
 * is a fatal redeclaration of WC().
 *
 * @return string Absolute path to woocommerce.php, or '' if it is not installed.
 */
function suitemart_find_woocommerce(): string {
	foreach ( (array) glob( WP_PLUGIN_DIR . '/*/woocommerce.php' ) as $candidate ) {
		// The plugin header is the only reliable identifier; a theme or a
		// helper plugin may perfectly well ship a file of the same name.
		$header = (string) file_get_contents( $candidate, false, null, 0, 2048 );

		if ( str_contains( $header, 'Plugin Name: WooCommerce' ) ) {
			return $candidate;
		}
	}

	return '';
}

/**
 * Loads WooCommerce, and refuses to run the suite without it.
 *
 * The test library loads no plugins of its own. Without this every commerce
 * block is unregistered and every test covering one reports as *skipped* — and
 * a run that skips its entire commerce surface reports green while testing
 * nothing at all. Suitemart is a WooCommerce theme, so that is a failed run
 * dressed up as a passing one, and it stops here rather than being left to the
 * reader to notice among the dots.
 *
 * @return void
 */
function suitemart_load_woocommerce_for_tests(): void {
	$plugin = suitemart_find_woocommerce();

	if ( '' === $plugin ) {
		$installed = array_map(
			'basename',
			array_filter( (array) glob( WP_PLUGIN_DIR . '/*' ), 'is_dir' )
		);

		echo "\nWooCommerce is not installed in the test environment, so every\n";
		echo "commerce test would skip and the run would report green while\n";
		echo "testing nothing. Refusing to continue.\n\n";
		// Console diagnostics for a CLI bootstrap, not page output.
		echo 'Looked in: ' . esc_html( WP_PLUGIN_DIR ) . "\n";
		echo 'Found: ' . esc_html( $installed ? implode( ', ', $installed ) : 'no plugins at all' ) . "\n\n";
		echo "Fix it with:\n";
		echo "  npx wp-env run tests-cli wp plugin install woocommerce --activate\n\n";

		exit( 1 );
	}

	require_once $plugin;
}
tests_add_filter( 'muplugins_loaded', 'suitemart_load_woocommerce_for_tests' );

require $_tests_dir . '/includes/bootstrap.php';
