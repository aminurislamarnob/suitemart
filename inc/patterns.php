<?php
/**
 * Pattern categories.
 *
 * The pattern files themselves live in /patterns and are registered
 * automatically by WordPress from their file headers. Only the categories they
 * reference need declaring here.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Registers Suitemart's pattern categories.
 *
 * @return void
 */
function suitemart_register_pattern_categories(): void {
	$categories = array(
		'suitemart/header'   => array(
			'label'       => __( 'Suitemart · Headers', 'suitemart' ),
			'description' => __( 'Header layouts, including mega menu arrangements.', 'suitemart' ),
		),
		'suitemart/footer'   => array(
			'label'       => __( 'Suitemart · Footers', 'suitemart' ),
			'description' => __( 'Footer layouts.', 'suitemart' ),
		),
		'suitemart/hero'     => array(
			'label'       => __( 'Suitemart · Heroes', 'suitemart' ),
			'description' => __( 'Above-the-fold sections: sliders, banners, promotions.', 'suitemart' ),
		),
		'suitemart/commerce' => array(
			'label'       => __( 'Suitemart · Commerce', 'suitemart' ),
			'description' => __( 'Product grids, category showcases and shop sections.', 'suitemart' ),
		),
		'suitemart/content'  => array(
			'label'       => __( 'Suitemart · Content', 'suitemart' ),
			'description' => __( 'Editorial sections: features, testimonials, teams, FAQs.', 'suitemart' ),
		),
		'suitemart/cta'      => array(
			'label'       => __( 'Suitemart · Calls to action', 'suitemart' ),
			'description' => __( 'Newsletter signups, promotions and conversion sections.', 'suitemart' ),
		),
		'suitemart/page'     => array(
			'label'       => __( 'Suitemart · Full pages', 'suitemart' ),
			'description' => __( 'Complete page layouts to start from.', 'suitemart' ),
		),
	);

	foreach ( $categories as $slug => $args ) {
		register_block_pattern_category( $slug, $args );
	}
}
add_action( 'init', 'suitemart_register_pattern_categories' );
