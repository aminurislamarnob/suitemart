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
 * The directory name is not `woocommerce/` on every install: wp-env names it
 * after the source it came from, so the zip URL in `.wp-env.json` produces
 * `woocommerce.latest-stable/` instead — and in practice leaves it empty, the
 * download having failed without saying so. Looking only for the conventional
 * name found nothing on any clean environment, which is why CI ran the entire
 * commerce suite as skips from the first commit while reporting green. It
 * passed locally purely because a manual `wp plugin install` had left a working
 * copy under the expected name.
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
