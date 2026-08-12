<?php
/**
 * Lightbox block.
 *
 * A container rather than a block that renders images of its own: whatever an
 * editor already knows how to build — a core gallery, a row of image blocks, a
 * single photograph — becomes one lightbox gallery by being put inside it. That
 * is also what distinguishes it from core's own image lightbox, which enlarges
 * one image at a time with no way to move between them.
 *
 * PhotoSwipe needs the full-size dimensions of each image up front, or it
 * cannot size the viewer before the file arrives and the first frame jumps.
 * Those dimensions are added here, on the server, from data WordPress already
 * has: core writes `wp-image-<id>` onto every inserted image, so the attachment
 * is identifiable without a database lookup per URL.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( '' === trim( $content ) ) {
	return '';
}

$sm_prepared = suitemart_lightbox_prepare( $content );

/*
 * Nothing inside links to an image, so there is nothing to enlarge. The content
 * still has to appear — an editor's gallery does not vanish because the
 * lightbox has no work to do — but the wrapper is dropped, and with it the init
 * directive that would otherwise download PhotoSwipe for a page that cannot use
 * it.
 *
 * Echoed rather than returned. A `render.php` is `require`d inside an output
 * buffer, so `return $content;` ends the file and hands back the buffer, which
 * is empty: the content disappears. `return '';` elsewhere in this codebase
 * works only because empty is what those blocks mean.
 */
if ( 0 === $sm_prepared['count'] ) {
	echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress.
	return;
}

/*
 * PhotoSwipe labels its own controls, in English, unless told otherwise. These
 * go through wp_interactivity_config() rather than state: they are the same for
 * every lightbox on the page and never change, which is exactly what config is
 * for, and it keeps them out of the per-instance context.
 */
wp_interactivity_config(
	'suitemart/lightbox',
	array(
		'closeTitle'     => __( 'Close', 'suitemart' ),
		'zoomTitle'      => __( 'Zoom', 'suitemart' ),
		'arrowPrevTitle' => __( 'Previous image', 'suitemart' ),
		'arrowNextTitle' => __( 'Next image', 'suitemart' ),
		'errorMsg'       => __( 'This image could not be loaded.', 'suitemart' ),
		'dialogLabel'    => __( 'Image viewer', 'suitemart' ),
	)
);

$sm_wrapper = get_block_wrapper_attributes( array( 'class' => 'sm-lightbox' ) );

$sm_context = array(
	'showCaptions' => ! empty( $attributes['showCaptions'] ),
	'loop'         => ! empty( $attributes['loop'] ),
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/lightbox"
	<?php echo wp_interactivity_data_wp_context( $sm_context ); ?>
	data-wp-init="callbacks.mount"
>
	<?php echo $sm_prepared['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress; the tag processor only rewrites attributes. ?>
</div>
