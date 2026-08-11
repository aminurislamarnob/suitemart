<?php
/**
 * Off-canvas trigger block.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_panel_id = isset( $attributes['panelId'] ) && is_string( $attributes['panelId'] ) && '' !== $attributes['panelId']
	? sanitize_key( $attributes['panelId'] )
	: 'panel';

$sm_label = isset( $attributes['label'] ) && is_string( $attributes['label'] ) && '' !== $attributes['label']
	? $attributes['label']
	: __( 'Open panel', 'suitemart' );

$sm_icon       = isset( $attributes['icon'] ) && is_string( $attributes['icon'] ) ? sanitize_key( $attributes['icon'] ) : '';
$sm_show_label = ! isset( $attributes['showLabel'] ) || (bool) $attributes['showLabel'];

$sm_wrapper = get_block_wrapper_attributes( array( 'class' => 'sm-off-canvas-trigger' ) );
?>
<button
	type="button"
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	aria-controls="<?php echo esc_attr( 'sm-off-canvas-' . $sm_panel_id ); ?>"
	<?php
	/*
	 * The button always carries its label, either visibly or for assistive
	 * technology only — an icon-only control with no accessible name is
	 * unusable, and it is the most common failure in commerce headers.
	 */
	if ( ! $sm_show_label ) :
		?>
		aria-label="<?php echo esc_attr( $sm_label ); ?>"
	<?php endif; ?>
	data-wp-interactive="suitemart/off-canvas"
	<?php echo wp_interactivity_data_wp_context( array( 'panelId' => $sm_panel_id ) ); ?>
	data-wp-bind--aria-expanded="state.isOpen"
	data-wp-on--click="actions.toggle"
>
	<?php if ( '' !== $sm_icon ) : ?>
		<?php echo suitemart_get_icon( $sm_icon, array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
	<?php endif; ?>
	<?php if ( $sm_show_label ) : ?>
		<span class="sm-off-canvas-trigger__label"><?php echo esc_html( $sm_label ); ?></span>
	<?php endif; ?>
</button>
