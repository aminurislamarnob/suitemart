<?php
/**
 * Frequently Bought Together block.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! suitemart_has_woocommerce() ) {
	return '';
}

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
if ( ! $sm_product || ! $sm_product->is_in_stock() || ! $sm_product->is_purchasable() ) {
	return '';
}

$sm_cross_sell_ids = $sm_product->get_cross_sell_ids();
if ( empty( $sm_cross_sell_ids ) ) {
	return '';
}

// We want the main product + up to 2 cross sells.
$sm_fbt_ids = array_merge( array( $sm_product_id ), array_slice( $sm_cross_sell_ids, 0, 2 ) );

$sm_valid_products = array();
$sm_product_prices = array();

foreach ( $sm_fbt_ids as $sm_id ) {
	$sm_p = wc_get_product( $sm_id );

	// Variable and grouped products cannot be added to the cart by id alone —
	// they need a variation or a child selection this block does not collect.
	if ( ! $sm_p || ! $sm_p->is_in_stock() || ! $sm_p->is_purchasable() || $sm_p->is_type( 'variable' ) || $sm_p->is_type( 'grouped' ) ) {
		continue;
	}
	$sm_valid_products[] = $sm_p;

	// Get price in minor units for JS formatting.
	$sm_price                    = wc_get_price_to_display( $sm_p );
	$sm_minor_price              = (int) round( (float) $sm_price * ( 10 ** wc_get_price_decimals() ) );
	$sm_product_prices[ $sm_id ] = $sm_minor_price;
}

if ( count( $sm_valid_products ) < 2 ) {
	return '';
}

/*
 * Prepare currency settings for the JS formatter, in the shape the Store API
 * uses. get_woocommerce_currency_symbol() returns an HTML entity ("&#36;"), and
 * the formatted total reaches the DOM through data-wp-text, which sets
 * textContent — so an undecoded symbol renders literally as "&#36;19.99".
 */
$sm_currency_pos = get_option( 'woocommerce_currency_pos', 'left' );
$sm_symbol       = html_entity_decode( get_woocommerce_currency_symbol(), ENT_QUOTES, 'UTF-8' );
$sm_prefix       = '';
$sm_suffix       = '';

switch ( $sm_currency_pos ) {
	case 'left':
		$sm_prefix = $sm_symbol;
		break;
	case 'right':
		$sm_suffix = $sm_symbol;
		break;
	case 'left_space':
		$sm_prefix = $sm_symbol . ' ';
		break;
	case 'right_space':
		$sm_suffix = ' ' . $sm_symbol;
		break;
}

$sm_currency_settings = array(
	'currency_minor_unit'         => wc_get_price_decimals(),
	'currency_thousand_separator' => wc_get_price_thousand_separator(),
	'currency_decimal_separator'  => wc_get_price_decimal_separator(),
	'currency_prefix'             => $sm_prefix,
	'currency_suffix'             => $sm_suffix,
);

$sm_context = array(
	'products'         => $sm_product_prices,
	'currencySettings' => $sm_currency_settings,
);

wp_interactivity_state(
	'suitemart/fbt-products',
	array(
		'selectedIds' => array_keys( $sm_product_prices ),
		'isAdding'    => false,
		'cartUrl'     => wc_get_cart_url(),
		'addItemUrl'  => rest_url( 'wc/store/v1/cart/add-item' ),

		/*
		 * No nonce is rendered here on purpose. A nonce baked into the markup
		 * goes stale behind a full-page cache and every add-to-cart then fails
		 * with a 403, so the browser reads a fresh one off the Store API at
		 * interaction time instead.
		 */
		'cartApiUrl'  => rest_url( 'wc/store/v1/cart' ),
		'error'       => '',
		'errorMsg'    => __( 'An error occurred while adding products to the cart.', 'suitemart' ),
	)
);

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-fbt-products' )
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	data-wp-interactive="suitemart/fbt-products"
	<?php echo wp_interactivity_data_wp_context( $sm_context ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
>
	<h3 class="sm-fbt-products__title"><?php esc_html_e( 'Frequently bought together', 'suitemart' ); ?></h3>

	<div class="sm-fbt-products__list">
		<?php foreach ( $sm_valid_products as $sm_p ) : ?>
			<div class="sm-fbt-products__item" data-wp-class--is-selected="callbacks.isSelected">
				<?php
				echo wp_interactivity_data_wp_context( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array( 'productId' => $sm_p->get_id() )
				);
				?>
				
				<div class="sm-fbt-products__item-image">
					<?php echo $sm_p->get_image( 'woocommerce_gallery_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>

				<div class="sm-fbt-products__item-details">
					<label class="sm-fbt-products__item-label">
						<input
							type="checkbox"
							class="sm-fbt-products__item-checkbox"
							data-wp-on--change="actions.toggleProduct"
							checked
						/>
						<span class="sm-fbt-products__item-name">
							<?php
							if ( $sm_p->get_id() === $sm_product_id ) {
								// translators: %s: Product title.
								echo wp_kses_post( sprintf( __( '<strong>This item:</strong> %s', 'suitemart' ), $sm_p->get_name() ) );
							} else {
								echo esc_html( $sm_p->get_name() );
							}
							?>
						</span>
					</label>
					<div class="sm-fbt-products__item-price">
						<?php echo wc_price( wc_get_price_to_display( $sm_p ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="sm-fbt-products__footer">
		<div class="sm-fbt-products__total">
			<span class="sm-fbt-products__total-label"><?php esc_html_e( 'Total price:', 'suitemart' ); ?></span>
			<span class="sm-fbt-products__total-price" data-wp-text="state.formattedTotal">
				<?php
				// Fallback server-rendered total.
				$sm_total_initial = 0;
				foreach ( $sm_valid_products as $sm_p ) {
					$sm_total_initial += wc_get_price_to_display( $sm_p );
				}
				echo wc_price( $sm_total_initial ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</span>
		</div>

		<div class="sm-fbt-products__error" data-wp-bind--hidden="!state.error">
			<p data-wp-text="state.error"></p>
		</div>

		<button
			type="button"
			class="sm-fbt-products__add-to-cart wp-element-button"
			data-wp-on--click="actions.addToCart"
			data-wp-bind--disabled="state.isAdding"
			data-wp-class--is-loading="state.isAdding"
		>
			<span class="sm-fbt-products__add-to-cart-text">
				<span data-wp-bind--hidden="state.isAdding"><?php esc_html_e( 'Add selected to cart', 'suitemart' ); ?></span>
				<span data-wp-bind--hidden="!state.isAdding"><?php esc_html_e( 'Adding...', 'suitemart' ); ?></span>
			</span>
		</button>
	</div>
</div>
