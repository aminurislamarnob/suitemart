<?php
/**
 * Environment compatibility guards.
 *
 * Suitemart declares a hard floor of WP 6.7 / PHP 8.1 (decision 19) because it
 * uses the Interactivity API, theme.json v3 and block style variations without
 * fallbacks. Running below that floor produces confusing partial breakage, so
 * the theme refuses to load its modules instead.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Collects every unmet environment requirement.
 *
 * @return array<int, string> Human-readable requirement failures; empty when supported.
 */
function suitemart_get_environment_problems(): array {
	$problems = array();

	if ( version_compare( PHP_VERSION, SUITEMART_MIN_PHP, '<' ) ) {
		$problems[] = sprintf(
			/* translators: 1: required PHP version, 2: current PHP version. */
			__( 'Suitemart requires PHP %1$s or newer. This site runs PHP %2$s.', 'suitemart' ),
			SUITEMART_MIN_PHP,
			PHP_VERSION
		);
	}

	if ( version_compare( get_bloginfo( 'version' ), SUITEMART_MIN_WP, '<' ) ) {
		$problems[] = sprintf(
			/* translators: 1: required WordPress version, 2: current WordPress version. */
			__( 'Suitemart requires WordPress %1$s or newer. This site runs WordPress %2$s.', 'suitemart' ),
			SUITEMART_MIN_WP,
			get_bloginfo( 'version' )
		);
	}

	return $problems;
}

/**
 * Whether the current environment meets Suitemart's supported floor.
 *
 * @return bool
 */
function suitemart_environment_is_supported(): bool {
	return array() === suitemart_get_environment_problems();
}

/**
 * Renders an admin notice listing unmet requirements.
 *
 * @return void
 */
function suitemart_render_unsupported_notice(): void {
	if ( ! current_user_can( 'switch_themes' ) ) {
		return;
	}

	$problems = suitemart_get_environment_problems();

	if ( array() === $problems ) {
		return;
	}

	printf(
		'<div class="notice notice-error"><p><strong>%s</strong></p><ul style="list-style:disc;padding-inline-start:2em;">',
		esc_html__( 'Suitemart cannot run on this site.', 'suitemart' )
	);

	foreach ( $problems as $problem ) {
		printf( '<li>%s</li>', esc_html( $problem ) );
	}

	echo '</ul></div>';
}

/**
 * Whether WooCommerce is active and new enough for Suitemart's commerce templates.
 *
 * Commerce templates and Woo-gap blocks call this before registering. WooCommerce
 * is optional: Suitemart is a usable general-purpose theme without it.
 *
 * @return bool
 */
function suitemart_has_woocommerce(): bool {
	if ( ! class_exists( 'WooCommerce' ) || ! defined( 'WC_VERSION' ) ) {
		return false;
	}

	return version_compare( (string) WC_VERSION, SUITEMART_MIN_WC, '>=' );
}
