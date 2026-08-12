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

wp_interactivity_state(
	'suitemart/size-guide',
	array(
		'openId' => '',
		'isOpen' => false,
	)
);

// Derived the same way as in the modal's render.php, so the pair agree without
// a shared ancestor to pass context through. See the note there.
$sm_post_id  = suitemart_clamp_int( $block->context['postId'] ?? get_the_ID(), 0, 0, PHP_INT_MAX );
$sm_modal_id = suitemart_size_guide_id( $sm_post_id, 'button' );

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-size-guide-button' )
);
?>
<button
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/size-guide"
	<?php echo wp_interactivity_data_wp_context( array( 'modalId' => $sm_modal_id ) ); ?>
	data-wp-on--click="actions.open"
	data-wp-bind--aria-expanded="state.isOpen"
	aria-expanded="false"
	aria-controls="<?php echo esc_attr( $sm_modal_id ); ?>"
	type="button"
>
	<?php echo esc_html( $sm_label ); ?>
</button>
