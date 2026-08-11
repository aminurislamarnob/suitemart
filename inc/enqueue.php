<?php
/**
 * Asset loading.
 *
 * Decision 16: there is no global bundle. Per-block stylesheets are declared in
 * each block.json and WordPress enqueues them only when the block is present.
 * This file loads the small global sheet, self-hosted fonts, and enables core's
 * per-block asset splitting.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * Load only the stylesheets a page actually needs. Without this, WordPress
 * prints one combined stylesheet containing every core block's CSS on every
 * request — the exact problem Suitemart exists to avoid.
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );

/**
 * Enqueues global front-end assets.
 *
 * @return void
 */
function suitemart_enqueue_assets(): void {
	$global_css = SUITEMART_DIR . '/build/global.css';

	if ( file_exists( $global_css ) ) {
		wp_enqueue_style(
			'suitemart-global',
			SUITEMART_URI . '/build/global.css',
			array(),
			(string) filemtime( $global_css )
		);
	}

	// The theme header carries no CSS, but WordPress and many plugins expect a
	// handle named after the stylesheet to exist. Register it empty.
	wp_register_style( 'suitemart-style', '', array(), SUITEMART_VERSION );
	wp_enqueue_style( 'suitemart-style' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'suitemart_enqueue_assets' );

/**
 * Preloads self-hosted variable fonts.
 *
 * Fonts registered in theme.json are emitted as @font-face by WordPress, but the
 * browser only discovers them after CSS parses. Preloading the two faces used
 * above the fold removes a render-blocking round trip.
 *
 * @return void
 */
function suitemart_preload_fonts(): void {
	$fonts = array(
		'/assets/fonts/inter-variable.woff2',
	);

	foreach ( $fonts as $font ) {
		if ( ! file_exists( SUITEMART_DIR . $font ) ) {
			continue;
		}

		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( SUITEMART_URI . $font )
		);
	}
}
add_action( 'wp_head', 'suitemart_preload_fonts', 1 );

/**
 * Enqueues editor-only assets.
 *
 * @return void
 */
function suitemart_enqueue_editor_assets(): void {
	$editor_css = SUITEMART_DIR . '/build/editor.css';

	if ( ! file_exists( $editor_css ) ) {
		return;
	}

	wp_enqueue_style(
		'suitemart-editor',
		SUITEMART_URI . '/build/editor.css',
		array(),
		(string) filemtime( $editor_css )
	);
}
add_action( 'enqueue_block_editor_assets', 'suitemart_enqueue_editor_assets' );

/**
 * Exposes the theme URI to editor scripts.
 *
 * Icon previews in the editor reference the sprite file by URL, because the
 * inlined sprite the front end uses is not present in the editor canvas. Every
 * block's editor script needs the same value, so it is printed once rather than
 * localised per handle.
 *
 * @return void
 */
function suitemart_print_editor_globals(): void {
	printf(
		'<script id="suitemart-editor-globals">window.suitemartThemeUri = %s;</script>',
		wp_json_encode( SUITEMART_URI )
	);
}
add_action( 'admin_print_scripts', 'suitemart_print_editor_globals' );
