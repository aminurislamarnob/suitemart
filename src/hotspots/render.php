<?php
/**
 * Image hotspots block.
 *
 * The parent owns the image and the marker appearance; each child owns its own
 * position and panel. Nothing is coordinated between them at render time, which
 * is deliberate — see the note in src/hotspot/render.php about why one open
 * panel at a time is arranged by the browser rather than by shared state.
 *
 * Marker numbering, when chosen, is a CSS counter. Passing an index down to
 * each child would couple the pair for something the stylesheet can count on
 * its own, and would renumber wrongly the moment a child is dragged.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup (the markers).
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_media_id  = suitemart_clamp_int( $attributes['mediaId'] ?? 0, 0, 0, PHP_INT_MAX );
$sm_media_url = isset( $attributes['mediaUrl'] ) && is_string( $attributes['mediaUrl'] ) ? $attributes['mediaUrl'] : '';

// Markers are positioned against the image. Without one there is nothing to pin
// them to, so the block renders nothing rather than a stack of floating dots.
if ( 0 === $sm_media_id && '' === $sm_media_url ) {
	return '';
}

$sm_alt   = isset( $attributes['mediaAlt'] ) && is_string( $attributes['mediaAlt'] ) ? $attributes['mediaAlt'] : '';
$sm_style = suitemart_enum( $attributes['markerStyle'] ?? 'plus', array( 'dot', 'plus', 'number' ), 'plus' );

$sm_classes = 'sm-hotspots sm-hotspots--' . $sm_style;

if ( ! empty( $attributes['pulse'] ) ) {
	$sm_classes .= ' has-pulse';
}

$sm_wrapper = get_block_wrapper_attributes( array( 'class' => $sm_classes ) );

$sm_image = '';

if ( $sm_media_id > 0 ) {
	$sm_image = wp_get_attachment_image(
		$sm_media_id,
		'large',
		false,
		array(
			'class' => 'sm-hotspots__image',
			'alt'   => $sm_alt,
		)
	);
} else {
	$sm_image = sprintf(
		'<img class="sm-hotspots__image" src="%s" alt="%s" loading="lazy" decoding="async" />',
		esc_url( $sm_media_url ),
		esc_attr( $sm_alt )
	);
}
?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<div class="sm-hotspots__frame">
		<?php echo $sm_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from wp_get_attachment_image() or escaped above. ?>
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
	</div>
</div>
