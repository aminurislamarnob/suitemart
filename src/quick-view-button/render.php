<?php
/**
 * Quick view button block.
 *
 * Renders a button that opens a modal to display product details fetched from
 * the Store API. The modal markup is injected into the footer once per page.
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

// Outside a product context there is nothing to quick view.
if ( 0 === $sm_product_id || 'product' !== get_post_type( $sm_product_id ) ) {
	return '';
}

$sm_appearance = suitemart_enum( $attributes['appearance'] ?? 'icon-label', array( 'icon', 'icon-label' ), 'icon-label' );
$sm_icon_size  = suitemart_clamp_int( $attributes['iconSize'] ?? 20, 20, 12, 48 );

wp_interactivity_state(
	'suitemart/quick-view',
	array(
		'isOpen'          => false,
		'activeProductId' => 0,
		'isLoading'       => false,
		'product'         => null,
		'errorText'       => __( 'Could not load product details.', 'suitemart' ),
		'statusText'      => '',
		'productsUrl'     => rest_url( 'wc/store/v1/products' ),
	)
);

// The modal itself is a single shared element rendered into the footer by
// inc/blocks/quick-view.php, no matter how many buttons are on the page.
suitemart_register_quick_view_modal();

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-quick-view-button sm-quick-view-button--' . $sm_appearance )
);
?>
<button
	type="button"
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	data-wp-interactive="suitemart/quick-view"
	<?php
	echo wp_interactivity_data_wp_context( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		array(
			'productId' => $sm_product_id,
		)
	);
	?>
	data-wp-on--click="actions.open"
	<?php
	/*
	 * In the icon-only appearance the visible label is gone, so the button
	 * would otherwise reach assistive technology with no accessible name at
	 * all. The label below is hidden from it in turn, to avoid announcing the
	 * same words twice when both are present.
	 */
	?>
	aria-label="<?php echo esc_attr__( 'Quick view', 'suitemart' ); ?>"
>
	<span class="sm-quick-view-button__icon">
		<?php echo suitemart_get_icon( 'search', array( 'size' => $sm_icon_size ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</span>
	<?php if ( 'icon-label' === $sm_appearance ) : ?>
		<span class="sm-quick-view-button__label" aria-hidden="true">
			<?php esc_html_e( 'Quick view', 'suitemart' ); ?>
		</span>
	<?php endif; ?>
</button>
