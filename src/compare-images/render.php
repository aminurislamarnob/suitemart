<?php
/**
 * Image comparison block.
 *
 * The control is a real `<input type="range">`, not a div with pointer
 * handlers. That decision buys the whole accessibility story for free —
 * keyboard operation, arrow and Home/End steps, a value announced as a
 * percentage, touch and pointer support, and the platform's own idea of what a
 * slider is — none of which a hand-rolled handle gets without a lot of code
 * that is easy to get subtly wrong. The native thumb is made transparent and
 * the visible handle is drawn separately, because a thumb sits inside the
 * track by half its own width and would drift away from the divider it is
 * supposed to be attached to.
 *
 * The wipe position rides in context as a finished style string rather than as
 * a number: directives are evaluated on the server too, and a `state` getter
 * that composes the string in JavaScript would resolve to null there, stripping
 * the style attribute and serving both images stacked at full width.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_before_id  = suitemart_clamp_int( $attributes['beforeId'] ?? 0, 0, 0, PHP_INT_MAX );
$sm_after_id   = suitemart_clamp_int( $attributes['afterId'] ?? 0, 0, 0, PHP_INT_MAX );
$sm_before_url = isset( $attributes['beforeUrl'] ) && is_string( $attributes['beforeUrl'] ) ? $attributes['beforeUrl'] : '';
$sm_after_url  = isset( $attributes['afterUrl'] ) && is_string( $attributes['afterUrl'] ) ? $attributes['afterUrl'] : '';

// One image is not a comparison. Rendering half of it would leave a slider that
// wipes between a photograph and nothing.
if ( ( 0 === $sm_before_id && '' === $sm_before_url ) || ( 0 === $sm_after_id && '' === $sm_after_url ) ) {
	return '';
}

$sm_before_alt = isset( $attributes['beforeAlt'] ) && is_string( $attributes['beforeAlt'] ) ? $attributes['beforeAlt'] : '';
$sm_after_alt  = isset( $attributes['afterAlt'] ) && is_string( $attributes['afterAlt'] ) ? $attributes['afterAlt'] : '';

$sm_before_label = isset( $attributes['beforeLabel'] ) && is_string( $attributes['beforeLabel'] ) ? trim( $attributes['beforeLabel'] ) : '';
$sm_after_label  = isset( $attributes['afterLabel'] ) && is_string( $attributes['afterLabel'] ) ? trim( $attributes['afterLabel'] ) : '';

$sm_orientation = suitemart_enum( $attributes['orientation'] ?? 'horizontal', array( 'horizontal', 'vertical' ), 'horizontal' );
$sm_position    = suitemart_clamp_int( $attributes['startPosition'] ?? 50, 50, 0, 100 );

$sm_frame_style = sprintf( '--sm-compare-position:%d%%;', $sm_position );

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-compare-images sm-compare-images--' . $sm_orientation )
);

// The before image is what a visitor sees first, so it is not lazy-loaded; the
// after image is clipped to a sliver until they move the handle.
$sm_before = suitemart_block_image( $sm_before_id, $sm_before_url, $sm_before_alt, 'sm-compare-images__image', 'large', true );
$sm_after  = suitemart_block_image( $sm_after_id, $sm_after_url, $sm_after_alt, 'sm-compare-images__image', 'large', false );

$sm_slider_label = 'vertical' === $sm_orientation
	? __( 'Reveal the second image, top to bottom', 'suitemart' )
	: __( 'Reveal the second image, left to right', 'suitemart' );
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/compare-images"
	<?php echo wp_interactivity_data_wp_context( array( 'frameStyle' => $sm_frame_style ) ); ?>
>
	<div
		class="sm-compare-images__frame"
		style="<?php echo esc_attr( $sm_frame_style ); ?>"
		data-wp-bind--style="context.frameStyle"
	>
		<?php echo $sm_before; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from wp_get_attachment_image() or escaped in the helper. ?>

		<div class="sm-compare-images__reveal">
			<?php echo $sm_after; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from wp_get_attachment_image() or escaped in the helper. ?>
		</div>

		<input
			type="range"
			class="sm-compare-images__range"
			min="0"
			max="100"
			step="1"
			value="<?php echo esc_attr( (string) $sm_position ); ?>"
			aria-label="<?php echo esc_attr( $sm_slider_label ); ?>"
			data-wp-on--input="actions.reveal"
		/>

		<?php
		/*
		 * Decorative twice over: the divider repeats the slider's value, which
		 * the input already announces, and the labels name images whose alt
		 * text already says what they are.
		 */
		?>
		<div class="sm-compare-images__divider" aria-hidden="true">
			<span class="sm-compare-images__handle"></span>
		</div>

		<?php if ( '' !== $sm_before_label ) : ?>
			<span class="sm-compare-images__label sm-compare-images__label--before" aria-hidden="true">
				<?php echo esc_html( $sm_before_label ); ?>
			</span>
		<?php endif; ?>

		<?php if ( '' !== $sm_after_label ) : ?>
			<span class="sm-compare-images__label sm-compare-images__label--after" aria-hidden="true">
				<?php echo esc_html( $sm_after_label ); ?>
			</span>
		<?php endif; ?>
	</div>
</div>
