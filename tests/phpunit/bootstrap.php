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

require $_tests_dir . '/includes/bootstrap.php';
