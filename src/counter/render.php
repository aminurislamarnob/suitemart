<?php
/**
 * Counter block.
 *
 * The final value is rendered server-side and is what a reader without
 * JavaScript — or with reduced-motion preferred — sees. The view module only
 * animates from the start value up to it, so the number is never wrong or
 * missing while scripts load.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_start    = suitemart_clamp_int( $attributes['start'] ?? 0, 0, -1000000000, 1000000000 );
$sm_end      = suitemart_clamp_int( $attributes['end'] ?? 100, 100, -1000000000, 1000000000 );
$sm_duration = suitemart_clamp_int( $attributes['duration'] ?? 2000, 2000, 200, 10000 );
$sm_size     = suitemart_clamp_int( $attributes['iconSize'] ?? 32, 32, 12, 128 );
$sm_align    = suitemart_enum( $attributes['alignment'] ?? 'center', array( 'start', 'center', 'end' ), 'center' );

$sm_prefix = isset( $attributes['prefix'] ) && is_string( $attributes['prefix'] ) ? $attributes['prefix'] : '';
$sm_suffix = isset( $attributes['suffix'] ) && is_string( $attributes['suffix'] ) ? $attributes['suffix'] : '';
$sm_label  = isset( $attributes['label'] ) && is_string( $attributes['label'] ) ? $attributes['label'] : '';
$sm_icon   = isset( $attributes['icon'] ) && is_string( $attributes['icon'] ) ? sanitize_key( $attributes['icon'] ) : '';

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-counter sm-counter--align-' . $sm_align )
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/counter"
	<?php
	echo wp_interactivity_data_wp_context( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
		array(
			'start'    => $sm_start,
			'end'      => $sm_end,
			'duration' => $sm_duration,
			'value'    => $sm_end,
		)
	);
	?>
	data-wp-init="callbacks.observe"
>
	<?php if ( '' !== $sm_icon ) : ?>
		<div class="sm-counter__icon">
			<?php
			echo suitemart_get_icon( $sm_icon, array( 'size' => $sm_size ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
			?>
		</div>
	<?php endif; ?>

	<p class="sm-counter__value">
		<?php if ( '' !== $sm_prefix ) : ?>
			<span class="sm-counter__affix"><?php echo esc_html( $sm_prefix ); ?></span>
		<?php endif; ?>
		<span class="sm-counter__number" data-wp-text="state.display"><?php echo esc_html( number_format_i18n( $sm_end ) ); ?></span>
		<?php if ( '' !== $sm_suffix ) : ?>
			<span class="sm-counter__affix"><?php echo esc_html( $sm_suffix ); ?></span>
		<?php endif; ?>
	</p>

	<?php if ( '' !== $sm_label ) : ?>
		<p class="sm-counter__label"><?php echo esc_html( $sm_label ); ?></p>
	<?php endif; ?>
</div>
