<?php
/**
 * Estimated delivery block.
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

// Don't show for virtual or downloadable products.
if ( ! $sm_product instanceof WC_Product || $sm_product->is_virtual() || $sm_product->is_downloadable() ) {
	return '';
}

$sm_min_days = suitemart_clamp_int( $attributes['minDays'] ?? 3, 3, 0, 365 );
$sm_max_days = suitemart_clamp_int( $attributes['maxDays'] ?? 5, 5, $sm_min_days, 365 );

/**
 * Calculates a future date by adding working days.
 *
 * @param int $days Number of working days to add.
 * @return DateTime Calculated date.
 */
$sm_get_delivery_date = function ( int $days ): DateTime {
	$date = new DateTime( 'now', wp_timezone() );
	while ( $days > 0 ) {
		$date->modify( '+1 day' );
		// 6 = Saturday, 7 = Sunday.
		if ( (int) $date->format( 'N' ) < 6 ) {
			--$days;
		}
	}
	return $date;
};

$sm_min_date = $sm_get_delivery_date( $sm_min_days );
$sm_max_date = $sm_get_delivery_date( $sm_max_days );

// Translators: date format for estimated delivery. See https://www.php.net/manual/en/datetime.format.php.
$sm_format  = _x( 'M j', 'Delivery date format', 'suitemart' );
$sm_min_str = wp_date( $sm_format, $sm_min_date->getTimestamp() );
$sm_max_str = wp_date( $sm_format, $sm_max_date->getTimestamp() );

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-estimated-delivery' )
);
?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<?php suitemart_get_icon( 'truck', array( 'size' => 20 ) ); ?>
	<span>
		<?php
		if ( $sm_min_str === $sm_max_str ) {
			printf(
				/* translators: %s: delivery date. */
				esc_html__( 'Estimated delivery: %s', 'suitemart' ),
				esc_html( $sm_min_str )
			);
		} else {
			printf(
				/* translators: 1: start date, 2: end date. */
				esc_html__( 'Estimated delivery: %1$s – %2$s', 'suitemart' ),
				esc_html( $sm_min_str ),
				esc_html( $sm_max_str )
			);
		}
		?>
	</span>
</div>
