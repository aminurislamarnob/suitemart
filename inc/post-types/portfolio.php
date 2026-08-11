<?php
/**
 * Portfolio post type.
 *
 * The only custom post type Suitemart registers (decision 11). Everything else
 * Woodmart modelled as a CPT maps onto a core primitive: reusable content →
 * synced patterns, sidebars → template parts, layouts → FSE templates, slides →
 * inner blocks.
 *
 * Because the theme carries this registration (decision 3), portfolio content is
 * orphaned when a user switches themes. That trade-off is accepted; the CPT is
 * kept `show_in_rest` and exportable so the content is always recoverable.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Registers the portfolio post type and its taxonomy.
 *
 * @return void
 */
function suitemart_register_portfolio(): void {
	register_post_type(
		'portfolio',
		array(
			'labels'            => array(
				'name'               => _x( 'Portfolio', 'post type general name', 'suitemart' ),
				'singular_name'      => _x( 'Project', 'post type singular name', 'suitemart' ),
				'add_new_item'       => __( 'Add Project', 'suitemart' ),
				'edit_item'          => __( 'Edit Project', 'suitemart' ),
				'new_item'           => __( 'New Project', 'suitemart' ),
				'view_item'          => __( 'View Project', 'suitemart' ),
				'search_items'       => __( 'Search Projects', 'suitemart' ),
				'not_found'          => __( 'No projects found.', 'suitemart' ),
				'not_found_in_trash' => __( 'No projects found in Trash.', 'suitemart' ),
				'all_items'          => __( 'All Projects', 'suitemart' ),
				'menu_name'          => __( 'Portfolio', 'suitemart' ),
			),
			'public'            => true,
			'has_archive'       => true,
			'show_in_rest'      => true,
			'menu_icon'         => 'dashicons-portfolio',
			'menu_position'     => 21,
			'supports'          => array(
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'revisions',
				'custom-fields',
				'author',
			),
			'rewrite'           => array(
				'slug'       => 'portfolio',
				'with_front' => false,
			),
			'show_in_nav_menus' => true,
			'template'          => array(
				array( 'core/paragraph', array( 'placeholder' => __( 'Describe this project…', 'suitemart' ) ) ),
			),
		)
	);

	register_taxonomy(
		'project-cat',
		array( 'portfolio' ),
		array(
			'labels'            => array(
				'name'          => _x( 'Project Categories', 'taxonomy general name', 'suitemart' ),
				'singular_name' => _x( 'Project Category', 'taxonomy singular name', 'suitemart' ),
				'search_items'  => __( 'Search Project Categories', 'suitemart' ),
				'all_items'     => __( 'All Project Categories', 'suitemart' ),
				'edit_item'     => __( 'Edit Project Category', 'suitemart' ),
				'add_new_item'  => __( 'Add Project Category', 'suitemart' ),
				'menu_name'     => __( 'Categories', 'suitemart' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'       => 'project-category',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'suitemart_register_portfolio' );

/**
 * Flushes rewrite rules once after the theme is activated.
 *
 * Without this the portfolio archive 404s until the user visits Permalinks.
 *
 * @return void
 */
function suitemart_flush_rewrites_on_activation(): void {
	suitemart_register_portfolio();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'suitemart_flush_rewrites_on_activation' );
