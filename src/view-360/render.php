<?php
/**
 * 360° view block.
 *
 * Every frame is in the markup at once and one of them is shown. The obvious
 * alternative — a single `<img>` whose `src` is rewritten — flashes white on
 * each step until the browser has the file cached, which on a turntable
 * sequence means the first full rotation looks broken.
 *
 * Which frame is showing is a `hidden` binding per frame, and the comparison
 * behind it is declared twice: once in JavaScript and once here as a PHP
 * closure that reads the same context. That duplication is the price of the
 * server rendering the right frame before any script loads, and it is worth
 * paying — the first version of this block moved a class imperatively from a
 * `data-wp-watch` callback, and Preact restored the class it knew about on the
 * next render, so the viewer skipped frames at random.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_frames = isset( $attributes['frames'] ) && is_array( $attributes['frames'] ) ? $attributes['frames'] : array();

$sm_frames = array_values(
	array_filter(
		array_map( 'intval', $sm_frames ),
		static fn ( int $id ): bool => $id > 0
	)
);

// A turntable sequence is normally 24 or 36 frames. The cap is generous but
// finite: the block downloads every frame, and a hand-edited attribute holding
// a thousand ids would otherwise try to.
$sm_frames = array_slice( $sm_frames, 0, 72 );

// Two frames is the minimum that can rotate. One is a photograph, and the
// image block renders those better.
if ( count( $sm_frames ) < 2 ) {
	return '';
}

if ( ! empty( $attributes['reverse'] ) ) {
	$sm_frames = array_reverse( $sm_frames );
}

$sm_label = isset( $attributes['label'] ) && is_string( $attributes['label'] ) && '' !== trim( $attributes['label'] )
	? trim( $attributes['label'] )
	: __( '360 degree view', 'suitemart' );

$sm_auto = ! empty( $attributes['autoRotate'] );

/*
 * Derived state, declared for the server exactly as view.js declares it for the
 * browser. wp_interactivity_get_context() is what makes this possible: the
 * closure runs once per element during directive processing, with that
 * element's own context in scope, so each frame is asked about itself.
 */
wp_interactivity_state(
	'suitemart/view-360',
	array(
		'isCurrentFrame' => static function (): bool {
			$sm_ctx = wp_interactivity_get_context();

			return isset( $sm_ctx['frame'], $sm_ctx['index'] ) && $sm_ctx['frame'] === $sm_ctx['index'];
		},
	)
);

$sm_wrapper = get_block_wrapper_attributes( array( 'class' => 'sm-view-360' ) );

$sm_context = array(
	'index'       => 0,
	'count'       => count( $sm_frames ),
	'dragging'    => false,
	'origin'      => 0,
	'originIndex' => 0,
	'autoRotate'  => $sm_auto,
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/view-360"
	<?php echo wp_interactivity_data_wp_context( $sm_context ); ?>
	data-wp-init="callbacks.start"
>
	<?php
	/*
	 * The stack is the image, so it carries the description and the individual
	 * frames are marked decorative. Giving each frame the same alt text would
	 * make a screen reader announce the object twenty-four times.
	 */
	?>
	<div
		class="sm-view-360__frames"
		role="img"
		aria-label="<?php echo esc_attr( $sm_label ); ?>"
		data-wp-on--pointerdown="actions.startDrag"
		data-wp-on-document--pointermove="actions.drag"
		data-wp-on-document--pointerup="actions.endDrag"
		data-wp-on-document--pointercancel="actions.endDrag"
	>
		<?php foreach ( $sm_frames as $sm_position => $sm_frame_id ) : ?>
			<?php
			echo wp_get_attachment_image(
				$sm_frame_id,
				'large',
				false,
				array_merge(
					array(
						'class'                => 'sm-view-360__frame',
						'alt'                  => '',
						// Every frame is needed the moment the visitor drags, and
						// a lazy frame arrives blank mid-rotation. Only the first
						// is eager, so the page still paints promptly.
						'loading'              => 0 === $sm_position ? 'eager' : 'lazy',
						'draggable'            => 'false',
						'data-wp-context'      => (string) wp_json_encode( array( 'frame' => $sm_position ) ),
						'data-wp-bind--hidden' => '!state.isCurrentFrame',
					),
					// Written out as well as bound, so the closed state is right in
					// the served HTML rather than corrected after hydration.
					0 === $sm_position ? array() : array( 'hidden' => 'hidden' )
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() escapes its output.
			?>
		<?php endforeach; ?>

		<span class="sm-view-360__hint" aria-hidden="true">
			<?php echo suitemart_get_icon( 'move-horizontal', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
			<?php echo esc_html__( 'Drag to spin', 'suitemart' ); ?>
		</span>
	</div>

	<div class="sm-view-360__controls">
		<button
			type="button"
			class="sm-view-360__button"
			data-wp-on--click="actions.previous"
		>
			<?php echo suitemart_get_icon( 'chevron-left', array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
			<span class="screen-reader-text"><?php echo esc_html__( 'Rotate left', 'suitemart' ); ?></span>
		</button>

		<?php if ( $sm_auto ) : ?>
			<?php
			/*
			 * One toggle rather than separate play and pause buttons, so focus
			 * stays put when it is used. Both icons are rendered and each is
			 * bound to context — which the server can resolve — instead of to a
			 * state getter, which it cannot.
			 */
			?>
			<button
				type="button"
				class="sm-view-360__button sm-view-360__button--play"
				aria-pressed="true"
				data-wp-bind--aria-pressed="context.autoRotate"
				data-wp-on--click="actions.toggleAutoRotate"
			>
				<span data-wp-bind--hidden="!context.autoRotate">
					<?php echo suitemart_get_icon( 'pause', array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
				</span>
				<span hidden data-wp-bind--hidden="context.autoRotate">
					<?php echo suitemart_get_icon( 'play', array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
				</span>
				<span class="screen-reader-text"><?php echo esc_html__( 'Rotate automatically', 'suitemart' ); ?></span>
			</button>
		<?php endif; ?>

		<button
			type="button"
			class="sm-view-360__button"
			data-wp-on--click="actions.next"
		>
			<?php echo suitemart_get_icon( 'chevron-right', array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
			<span class="screen-reader-text"><?php echo esc_html__( 'Rotate right', 'suitemart' ); ?></span>
		</button>
	</div>
</div>
