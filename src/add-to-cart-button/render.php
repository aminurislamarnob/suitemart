<?php
/**
 * Add to cart button block.
 *
 * The icon-only add to cart a product card's action bar needs.
 * `woocommerce/product-button` is text-only — its block has no icon form to ask
 * for — and at button size it is twice the height of the marks beside it.
 *
 * Only a product that can actually be added in one request gets a button. A
 * variable product needs its options chosen, an external one lives on someone
 * else's site, and one that is out of stock cannot be added at all; each of
 * those renders as a link to the product instead, carrying the same mark and
 * Woo's own wording for what the click will do. A button that silently fails is
 * worse than a link that goes where it says.
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

$sm_appearance = suitemart_enum( $attributes['appearance'] ?? 'icon', array( 'icon', 'icon-label' ), 'icon' );
$sm_icon_size  = suitemart_clamp_int( $attributes['iconSize'] ?? 20, 20, 12, 48 );

// Woo's own wording, so "Select options" and "Read more" read the way they do
// everywhere else in the shop.
$sm_label = $sm_product->add_to_cart_text();

$sm_can_add = $sm_product->supports( 'ajax_add_to_cart' )
	&& $sm_product->is_purchasable()
	&& $sm_product->is_in_stock();

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-add-to-cart sm-add-to-cart--' . $sm_appearance )
);

$sm_icon       = suitemart_get_icon( 'shopping-cart', array( 'size' => $sm_icon_size ) );
$sm_icon_added = suitemart_get_icon( 'check', array( 'size' => $sm_icon_size ) );
$sm_show_label = 'icon-label' === $sm_appearance;

if ( ! $sm_can_add ) :
	?>
	<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
		<a
			class="sm-add-to-cart__button"
			href="<?php echo esc_url( $sm_product->add_to_cart_url() ); ?>"
			<?php if ( ! $sm_show_label ) : ?>
				aria-label="<?php echo esc_attr( $sm_label ); ?>"
			<?php endif; ?>
		>
			<span class="sm-add-to-cart__icon">
				<?php echo $sm_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
			</span>
			<?php if ( $sm_show_label ) : ?>
				<span class="sm-add-to-cart__label"><?php echo esc_html( $sm_label ); ?></span>
			<?php endif; ?>
		</a>
	</div>
	<?php
	return;
endif;

/*
 * Shared by every instance on the page, and none of it is per-product. The
 * nonce is deliberately absent: one rendered into the markup is served stale
 * from behind a full-page cache and every add then fails with a 403, so the
 * browser reads a fresh one off the Store API at interaction time.
 */
wp_interactivity_state(
	'suitemart/add-to-cart',
	array(
		'cartApiUrl'  => rest_url( 'wc/store/v1/cart' ),
		'addItemUrl'  => rest_url( 'wc/store/v1/cart/add-item' ),
		'addingLabel' => __( 'Adding…', 'suitemart' ),
		'addedLabel'  => __( 'Added to cart', 'suitemart' ),
		'addedNotice' => __( 'Added to your cart', 'suitemart' ),
		'errorMsg'    => __( 'This product could not be added to your cart.', 'suitemart' ),
	)
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/add-to-cart"
	<?php
	/*
	 * Declared here rather than on the button, because the status region below
	 * is the button's sibling and context only flows downwards — bound to the
	 * button, `context.notice` would resolve to nothing in the one element
	 * whose whole job is to carry it.
	 *
	 * Every bound value is seeded, and written as a literal beside its binding.
	 * An expression the server cannot resolve does not leave the previous value
	 * in place — it strips the attribute, which here would mean a button with
	 * no accessible name at all.
	 */
	echo wp_interactivity_data_wp_context( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
		array(
			'productId' => $sm_product_id,
			'label'     => $sm_label,
			'isAdding'  => false,
			'isAdded'   => false,
			'notice'    => '',
		)
	);
	?>
>
	<button
		type="button"
		class="sm-add-to-cart__button"
		data-wp-on--click="actions.add"
		data-wp-class--is-adding="context.isAdding"
		data-wp-class--is-added="context.isAdded"
		data-wp-bind--aria-label="context.label"
		data-wp-bind--aria-busy="context.isAdding"
		data-wp-bind--disabled="context.isAdding"
		aria-label="<?php echo esc_attr( $sm_label ); ?>"
	>
		<span class="sm-add-to-cart__icon" data-wp-bind--hidden="context.isAdded">
			<?php echo $sm_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
		</span>
		<span class="sm-add-to-cart__icon" data-wp-bind--hidden="!context.isAdded" hidden>
			<?php echo $sm_icon_added; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
		</span>
		<?php if ( $sm_show_label ) : ?>
			<span class="sm-add-to-cart__label" data-wp-text="context.label"><?php echo esc_html( $sm_label ); ?></span>
		<?php endif; ?>
	</button>

	<?php
	/*
	 * Per instance, not in global state: a shop page carries twelve of these
	 * and global state is global to every one of them, so one add would
	 * announce from all twelve at once.
	 */
	?>
	<span class="sm-add-to-cart__status" role="status" data-wp-text="context.notice"></span>
</div>
