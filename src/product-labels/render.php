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
	/*
	 * "-20%" rather than "Sale" wherever the saving can actually be worked out:
	 * the number is the thing worth reading, and it is the same number the
	 * shopper would otherwise have to get by comparing two prices.
	 *
	 * Both prices are read as floats and the regular one is checked for zero
	 * before dividing — a variable product's range, a subscription, or a
	 * product priced by a plugin can all answer with an empty string, and
	 * `(float) '' ` is a division by zero. Where the sum cannot be trusted the
	 * label falls back to the word.
	 */
	$sm_regular = (float) $sm_product->get_regular_price();
	$sm_sale    = (float) $sm_product->get_sale_price();

	$sm_sale_text = __( 'Sale', 'suitemart' );

	if ( $sm_regular > 0 && $sm_sale > 0 && $sm_sale < $sm_regular ) {
		$sm_percentage = (int) round( ( ( $sm_regular - $sm_sale ) / $sm_regular ) * 100 );

		if ( $sm_percentage > 0 ) {
			$sm_sale_text = sprintf(
				/* translators: %d: discount as a percentage, without the sign. */
				__( '-%d%%', 'suitemart' ),
				$sm_percentage
			);
		}
	}

	$sm_labels[] = sprintf(
		'<span class="sm-product-labels__label sm-product-labels__label--sale">%s</span>',
		esc_html( $sm_sale_text )
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
