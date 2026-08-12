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
 * Loads WooCommerce when the environment has it.
 *
 * The test library loads no plugins of its own, so without this every commerce
 * block is unregistered and every test covering one reports as skipped — which
 * reads as a green run while testing nothing. It stays optional so the suite
 * still runs on a plain WordPress checkout, where the commerce blocks are
 * expected to be absent and are asserted to be.
 *
 * @return void
 */
function suitemart_load_woocommerce_for_tests(): void {
	$plugin = WP_PLUGIN_DIR . '/woocommerce/woocommerce.php';

	if ( file_exists( $plugin ) ) {
		require_once $plugin;
	}
}
tests_add_filter( 'muplugins_loaded', 'suitemart_load_woocommerce_for_tests' );

require $_tests_dir . '/includes/bootstrap.php';
