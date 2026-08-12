<?php
/**
 * Product labels block.
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

if ( ! $sm_product instanceof WC_Product ) {
	return '';
}

$sm_labels = array();

if ( $sm_product->is_on_sale() ) {
	$sm_labels[] = sprintf(
		'<span class="sm-product-labels__label sm-product-labels__label--sale">%s</span>',
		esc_html__( 'Sale', 'suitemart' )
	);
}

// A product is "new" if created in the last N days, adjustable by filter.
$sm_new_days = (int) apply_filters( 'suitemart_product_new_days', 7 );

if ( $sm_new_days > 0 ) {
	$sm_created = $sm_product->get_date_created();

	if ( $sm_created ) {
		$sm_diff = ( time() - $sm_created->getTimestamp() ) / DAY_IN_SECONDS;

		if ( $sm_diff <= $sm_new_days ) {
			$sm_labels[] = sprintf(
				'<span class="sm-product-labels__label sm-product-labels__label--new">%s</span>',
				esc_html__( 'New', 'suitemart' )
			);
		}
	}
}

if ( ! $sm_product->is_in_stock() ) {
	$sm_labels[] = sprintf(
		'<span class="sm-product-labels__label sm-product-labels__label--out-of-stock">%s</span>',
		esc_html__( 'Out of stock', 'suitemart' )
	);
}

if ( empty( $sm_labels ) ) {
	return '';
}

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-product-labels' )
);
?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<?php echo implode( '', $sm_labels ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped when building $sm_labels above. ?>
</div>
