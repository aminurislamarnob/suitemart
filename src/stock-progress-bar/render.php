<?php
/**
 * Stock progress bar block.
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

if ( ! $sm_product->managing_stock() ) {
	return '';
}

$sm_stock = $sm_product->get_stock_quantity();

if ( null === $sm_stock || $sm_stock <= 0 ) {
	return '';
}

// WooCommerce does not store "initial stock", so this is inferred from current
// stock plus the total number of units sold. This correctly shows a full bar
// for a new product, and a depleting bar as it sells.
$sm_total_sales   = (int) get_post_meta( $sm_product_id, 'total_sales', true );
$sm_initial_stock = $sm_stock + $sm_total_sales;

if ( $sm_initial_stock <= 0 ) {
	$sm_initial_stock = $sm_stock;
}

$sm_percentage = round( ( $sm_stock / $sm_initial_stock ) * 100, 2 );
$sm_percentage = min( max( $sm_percentage, 0 ), 100 ); // Clamp to 0-100.

$sm_low_stock = wc_get_low_stock_amount( $sm_product );
$sm_is_low    = $sm_stock <= $sm_low_stock;
$sm_classes   = 'sm-stock-progress-bar';

if ( $sm_is_low ) {
	$sm_classes .= ' is-low-stock';
}

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => $sm_classes )
);
?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<div class="sm-stock-progress-bar__message">
		<?php
		/*
		 * "Only N left" is a scarcity claim, so it is reserved for stock that
		 * has actually fallen to the shop's own low-stock threshold. Above it,
		 * the bar states the level plainly instead.
		 */
		if ( $sm_is_low ) {
			printf(
				/* translators: %s: quantity remaining in stock. */
				esc_html( _n( 'Only %s left', 'Only %s left', (int) $sm_stock, 'suitemart' ) ),
				esc_html( number_format_i18n( $sm_stock ) )
			);
		} else {
			printf(
				/* translators: %s: quantity remaining in stock. */
				esc_html( _n( '%s in stock', '%s in stock', (int) $sm_stock, 'suitemart' ) ),
				esc_html( number_format_i18n( $sm_stock ) )
			);
		}
		?>
	</div>
	<div class="sm-stock-progress-bar__track">
		<div
			class="sm-stock-progress-bar__fill"
			style="width: <?php echo esc_attr( $sm_percentage . '%' ); ?>"
		></div>
	</div>
</div>
