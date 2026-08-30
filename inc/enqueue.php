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

	if ( file_exists( $editor_css ) ) {
		wp_enqueue_style(
			'suitemart-editor',
			SUITEMART_URI . '/build/editor.css',
			array(),
			(string) filemtime( $editor_css )
		);
	}

	$editor_js = SUITEMART_DIR . '/build/editor.js';
	$asset     = SUITEMART_DIR . '/build/editor.asset.php';

	if ( ! file_exists( $editor_js ) || ! file_exists( $asset ) ) {
		return;
	}

	$meta = require $asset;

	wp_enqueue_script(
		'suitemart-editor',
		SUITEMART_URI . '/build/editor.js',
		(array) ( $meta['dependencies'] ?? array() ),
		(string) ( $meta['version'] ?? filemtime( $editor_js ) ),
		true
	);

	/*
	 * The canvas needs the sprite as markup, not as a stylesheet, so it is
	 * fetched by build/editor.js rather than printed here — see that file for
	 * why it cannot simply be inlined into the admin page the way the front end
	 * inlines it into the page it renders.
	 */
	wp_add_inline_script(
		'suitemart-editor',
		sprintf(
			'window.suitemartEditor = %s;',
			wp_json_encode( array( 'spriteUrl' => SUITEMART_URI . '/assets/icons/sprite.svg' ) )
		),
		'before'
	);
}
add_action( 'enqueue_block_editor_assets', 'suitemart_enqueue_editor_assets' );
