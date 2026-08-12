<?php
/**
 * Sold counter block.
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

$sm_total_sales = (int) get_post_meta( $sm_product_id, 'total_sales', true );

if ( $sm_total_sales <= 0 ) {
	return '';
}

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-sold-counter' )
);
?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<?php echo suitemart_get_icon( 'trending-up', array( 'size' => 16 ) ); ?>
	<span>
		<?php
		printf(
			/* translators: %s: number of units sold. */
			esc_html( _n( '%s unit sold', '%s units sold', $sm_total_sales, 'suitemart' ) ),
			esc_html( number_format_i18n( $sm_total_sales ) )
		);
		?>
	</span>
</div>
