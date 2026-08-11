<?php
/**
 * Third-party integration loader.
 *
 * Each integration lives in its own file and is loaded only when its plugin is
 * actually active, so an unused integration costs nothing at runtime and cannot
 * fatal on a missing class.
 *
 * Integrations land in P4; this loader exists from P1 so that adding one is a
 * single-file change with no wiring.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Maps integration files to a predicate deciding whether to load them.
 *
 * @return array<string, callable(): bool>
 */
function suitemart_integration_map(): array {
	return array(
		'woocommerce' => 'suitemart_has_woocommerce',
	);
}

/**
 * Loads every applicable integration.
 *
 * @return void
 */
function suitemart_load_integrations(): void {
	foreach ( suitemart_integration_map() as $slug => $is_active ) {
		if ( ! $is_active() ) {
			continue;
		}

		$file = SUITEMART_DIR . '/inc/integrations/' . $slug . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}
add_action( 'after_setup_theme', 'suitemart_load_integrations', 20 );
