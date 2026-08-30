<?php
/**
 * Block registration.
 *
 * Every block in src/ is compiled to build/<block>/block.json by wp-scripts and
 * discovered here. Blocks never register themselves, and nothing is listed by
 * hand — adding a directory under src/ is all it takes to ship a block.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Blocks that require WooCommerce.
 *
 * Registering a commerce block without Woo produces fatals inside render.php, so
 * these are skipped entirely when Woo is absent or too old.
 *
 * @return array<int, string> Block directory names.
 */
function suitemart_woocommerce_block_slugs(): array {
	return array(
		'add-to-cart-button',
		'product-gallery',
		'product-labels',
		'size-guide',
		'size-guide-button',
		'sold-counter',
		'stock-progress-bar',
		'visitor-counter',
		'wishlist-button',
		'wishlist-grid',
		'compare-button',
		'compare-table',
		'quick-view-button',
		'fbt-products',
		'estimated-delivery',
		'product-countdown',
	);
}

/**
 * Registers every built block.
 *
 * @return void
 */
function suitemart_register_blocks(): void {
	$manifest = SUITEMART_DIR . '/build/blocks-manifest.php';

	// WordPress 6.8+ can register a whole collection from a generated manifest,
	// which avoids one filesystem read per block. Fall back to globbing on 6.7.
	if ( file_exists( $manifest ) && function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
		wp_register_block_types_from_metadata_collection( SUITEMART_DIR . '/build', $manifest );
		suitemart_unregister_unavailable_blocks();
		return;
	}

	$block_json_files = glob( SUITEMART_DIR . '/build/*/block.json' );

	if ( false === $block_json_files ) {
		return;
	}

	$skip = suitemart_has_woocommerce() ? array() : suitemart_woocommerce_block_slugs();

	foreach ( $block_json_files as $block_json ) {
		if ( in_array( basename( dirname( $block_json ) ), $skip, true ) ) {
			continue;
		}

		register_block_type( $block_json );
	}
}
add_action( 'init', 'suitemart_register_blocks' );

/**
 * Removes commerce blocks when WooCommerce is unavailable.
 *
 * Only needed on the manifest path, which registers everything in one call.
 *
 * @return void
 */
function suitemart_unregister_unavailable_blocks(): void {
	if ( suitemart_has_woocommerce() ) {
		return;
	}

	$registry = WP_Block_Type_Registry::get_instance();

	foreach ( suitemart_woocommerce_block_slugs() as $slug ) {
		$name = 'suitemart/' . $slug;

		if ( $registry->is_registered( $name ) ) {
			unregister_block_type( $name );
		}
	}
}

/**
 * Adds the Suitemart block categories to the inserter.
 *
 * Two categories rather than one: a 60-block flat list is unusable, and
 * separating commerce blocks matches how users think about them.
 *
 * @param array<int, array<string, mixed>> $categories Existing categories.
 * @return array<int, array<string, mixed>>
 */
function suitemart_block_categories( array $categories ): array {
	return array_merge(
		array(
			array(
				'slug'  => 'suitemart',
				'title' => __( 'Suitemart', 'suitemart' ),
				'icon'  => null,
			),
			array(
				'slug'  => 'suitemart-commerce',
				'title' => __( 'Suitemart Commerce', 'suitemart' ),
				'icon'  => null,
			),
		),
		$categories
	);
}
add_filter( 'block_categories_all', 'suitemart_block_categories' );
