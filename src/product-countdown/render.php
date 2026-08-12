<?php
/**
 * Product countdown block.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_product_id = suitemart_clamp_int(
	$block->context['postId'] ?? get_the_ID(),
	0,
	0,
	PHP_INT_MAX
);

if ( 0 === $sm_product_id || 'product' !== get_post_type( $sm_product_id ) ) {
	return '';
}

$sm_product = wc_get_product( $sm_product_id );

if ( ! $sm_product instanceof WC_Product || ! $sm_product->is_on_sale() ) {
	return '';
}

$sm_sale_end = $sm_product->get_date_on_sale_to();

if ( null === $sm_sale_end ) {
	return '';
}

// suitemart/countdown expects a datetime-local string like '2023-12-31T23:59'.
$sm_end_date = wp_date( 'Y-m-d\TH:i:s', $sm_sale_end->getTimestamp() );

$sm_countdown_attrs = array(
	'endDate'     => $sm_end_date,
	'layout'      => $attributes['layout'] ?? 'inline',
	'expiredText' => __( 'Sale has ended.', 'suitemart' ),
);

if ( isset( $attributes['units'] ) ) {
	$sm_countdown_attrs['units'] = $attributes['units'];
}

$sm_content = render_block(
	array(
		'blockName'    => 'suitemart/countdown',
		'attrs'        => $sm_countdown_attrs,
		'innerBlocks'  => array(),
		'innerHTML'    => '',
		'innerContent' => array(),
	)
);

// get_block_wrapper_attributes() on the wrapper ensures any block supports like margins work.
$sm_wrapper = get_block_wrapper_attributes( array( 'class' => 'sm-product-countdown-wrapper' ) );
?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<?php echo $sm_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_block returns safe HTML. ?>
</div>
