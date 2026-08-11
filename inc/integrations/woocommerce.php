<?php
/**
 * WooCommerce integration.
 *
 * Suitemart composes its commerce templates from WooCommerce's own blocks
 * (decision 15) and never replaces Cart, Checkout or My Account (decision 5).
 * This file's job is therefore narrow: stop WooCommerce injecting classic-theme
 * markup around block templates, and remove the legacy styles the block
 * templates do not need.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Removes WooCommerce's classic content wrappers.
 *
 * These hooks emit `<div id="primary">` style markup designed for classic
 * themes. In a block theme the template already provides the document
 * structure, so leaving them in produces duplicated, unstyled containers.
 *
 * @return void
 */
function suitemart_remove_woocommerce_wrappers(): void {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
}
add_action( 'init', 'suitemart_remove_woocommerce_wrappers' );

/**
 * Dequeues WooCommerce's classic-theme stylesheets.
 *
 * `woocommerce-general` and `woocommerce-layout` style the PHP templates
 * Suitemart does not use. The smallcart/blocks styles are kept because the
 * Cart and Checkout blocks genuinely depend on them.
 *
 * @param array<string, mixed> $styles Registered WooCommerce styles.
 * @return array<string, mixed>
 */
function suitemart_filter_woocommerce_styles( array $styles ): array {
	unset( $styles['woocommerce-general'], $styles['woocommerce-layout'], $styles['woocommerce-smallscreen'] );

	return $styles;
}
add_filter( 'woocommerce_enqueue_styles', 'suitemart_filter_woocommerce_styles' );

/**
 * Sets how many products a shop archive shows per page.
 *
 * Product Collection blocks in Suitemart templates set their own query, so this
 * only affects fallback archives.
 *
 * @return int
 */
function suitemart_products_per_page(): int {
	return 12;
}
add_filter( 'loop_shop_per_page', 'suitemart_products_per_page', 20 );

/**
 * Declares compatibility with WooCommerce's High-Performance Order Storage.
 *
 * Themes are not required to declare HPOS compatibility, but doing so keeps the
 * WooCommerce status report clean for buyers who check it.
 *
 * @return void
 */
function suitemart_declare_hpos_compatibility(): void {
	if ( ! class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		return;
	}

	\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
		'custom_order_tables',
		get_template_directory() . '/functions.php',
		true
	);
}
add_action( 'before_woocommerce_init', 'suitemart_declare_hpos_compatibility' );
