<?php
/**
 * Size guide button block.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_label = isset( $attributes['label'] ) && '' !== $attributes['label']
	? $attributes['label']
	: __( 'Size guide', 'suitemart' );

// Seed state.
wp_interactivity_state( 'suitemart/size-guide', array( 'isOpen' => false ) );

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-size-guide-button' )
);
?>
<button
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/size-guide"
	data-wp-on--click="actions.open"
	data-wp-bind--aria-expanded="state.isOpen"
	aria-controls="sm-size-guide-modal"
	type="button"
>
	<?php echo esc_html( $sm_label ); ?>
</button>
