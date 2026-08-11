<?php
/**
 * Marquee block.
 *
 * Renders a plain, static, horizontally scrollable row. The looping animation
 * is added by the view module only after it has cloned the track, because a
 * seamless loop needs two copies of the content and duplicating it server-side
 * would duplicate every `id` inside it.
 *
 * That ordering is also the accessibility story: with no JavaScript, or with
 * reduced motion preferred, nothing moves and every item is still reachable.
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

$sm_speed     = suitemart_clamp_int( $attributes['speed'] ?? 60, 60, 5, 400 );
$sm_direction = suitemart_enum( $attributes['direction'] ?? 'end', array( 'start', 'end' ), 'end' );
$sm_hover     = ! empty( $attributes['pauseOnHover'] );

$sm_label = isset( $attributes['ariaLabel'] ) && is_string( $attributes['ariaLabel'] ) && '' !== $attributes['ariaLabel']
	? $attributes['ariaLabel']
	: __( 'Announcements', 'suitemart' );

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'sm-marquee sm-marquee--' . $sm_direction . ( $sm_hover ? ' sm-marquee--pause-on-hover' : '' ),
	)
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/marquee"
	<?php
	echo wp_interactivity_data_wp_context( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
		array(
			'speed'       => $sm_speed,
			'isAnimating' => false,
			'isPlaying'   => true,
		)
	);
	?>
	data-wp-init="callbacks.setup"
	data-wp-class--is-animating="context.isAnimating"
	data-wp-class--is-paused="!context.isPlaying"
	role="group"
	aria-label="<?php echo esc_attr( $sm_label ); ?>"
>
	<div class="sm-marquee__viewport">
		<div class="sm-marquee__lane">
			<div class="sm-marquee__track">
				<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
			</div>
		</div>
	</div>

	<button
		type="button"
		class="sm-marquee__toggle"
		hidden
		data-wp-bind--hidden="!context.isAnimating"
		data-wp-on--click="actions.toggle"
		data-wp-bind--aria-label="state.toggleLabel"
		<?php // Both labels are shipped up front: the server cannot know which state the button will be in when it is read. ?>
		data-label-pause="<?php echo esc_attr__( 'Pause the scrolling strip', 'suitemart' ); ?>"
		data-label-play="<?php echo esc_attr__( 'Resume the scrolling strip', 'suitemart' ); ?>"
		aria-label="<?php echo esc_attr__( 'Pause the scrolling strip', 'suitemart' ); ?>"
	>
		<span class="sm-marquee__toggle-icon" data-wp-bind--hidden="!context.isPlaying">
			<?php
			echo suitemart_get_icon( 'pause', array( 'size' => 16 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
			?>
		</span>
		<span class="sm-marquee__toggle-icon" data-wp-bind--hidden="context.isPlaying">
			<?php
			echo suitemart_get_icon( 'play', array( 'size' => 16 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
			?>
		</span>
	</button>
</div>
