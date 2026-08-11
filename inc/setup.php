<?php
/**
 * Theme setup.
 *
 * Block themes get most supports implicitly, so this file declares only what is
 * still opt-in, plus the WooCommerce declarations that stop Woo from injecting
 * its own classic templates over ours.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Registers theme supports.
 *
 * @return void
 */
function suitemart_setup(): void {
	load_theme_textdomain( 'suitemart', SUITEMART_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	// Loaded into the editor iframe so blocks look the same there as on the front end.
	add_editor_style( 'build/editor.css' );

	if ( suitemart_has_woocommerce() ) {
		// Declares that Suitemart provides its own block templates for commerce
		// pages, so WooCommerce does not fall back to its PHP templates.
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
}
add_action( 'after_setup_theme', 'suitemart_setup' );

/**
 * Registers the theme's image sizes.
 *
 * Product and portfolio cards use fixed aspect ratios; hard-cropping at upload
 * time avoids layout shift in grids.
 *
 * @return void
 */
function suitemart_image_sizes(): void {
	add_image_size( 'suitemart-card', 600, 750, true );
	add_image_size( 'suitemart-card-wide', 900, 600, true );
	add_image_size( 'suitemart-hero', 1920, 1080, true );
}
add_action( 'after_setup_theme', 'suitemart_image_sizes' );

/**
 * Registers the template part areas exposed in the Site Editor.
 *
 * @param array<int, array<string, mixed>> $areas Existing template part areas.
 * @return array<int, array<string, mixed>>
 */
function suitemart_template_part_areas( array $areas ): array {
	$areas[] = array(
		'area'        => 'navigation',
		'area_tag'    => 'nav',
		'label'       => __( 'Navigation', 'suitemart' ),
		'description' => __( 'Mega menu panels and navigation areas.', 'suitemart' ),
		'icon'        => 'layout',
	);

	return $areas;
}
add_filter( 'default_wp_template_part_areas', 'suitemart_template_part_areas' );
