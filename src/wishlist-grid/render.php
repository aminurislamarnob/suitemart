<?php
/**
 * Wishlist grid block.
 *
 * Like the comparison table, this renders nothing on the server: the saved list
 * is in the visitor's browser, so the server emits the empty state and the
 * browser fetches the products from the Store API.
 *
 * The wishlist has no ceiling, unlike the comparison list — a reader can save
 * as many products as they like — so this fetches in pages rather than assuming
 * one request covers the list.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_columns    = suitemart_clamp_int( $attributes['columns'] ?? 4, 4, 1, 6 );
$sm_show_price = ( $attributes['showPrice'] ?? true ) !== false;
$sm_show_stock = ( $attributes['showStock'] ?? true ) !== false;

/*
 * Seeded so every binding resolves on the server too. A `data-wp-bind` the
 * server cannot resolve strips its attribute, and a `data-wp-text` it cannot
 * resolve empties the element — so the honest initial state is stated here
 * rather than left undefined.
 */
wp_interactivity_state(
	'suitemart/wishlist',
	array(
		'productsUrl'  => rest_url( 'wc/store/v1/products' ),
		'hasProducts'  => false,
		'isEmpty'      => true,
		'statusText'   => '',
		'errorText'    => __( 'Your wishlist could not be loaded. Please try again.', 'suitemart' ),
		'removeLabel'  => __( 'Remove from wishlist', 'suitemart' ),
		'viewLabel'    => __( 'View product', 'suitemart' ),
		'inStockText'  => __( 'In stock', 'suitemart' ),
		'outStockText' => __( 'Out of stock', 'suitemart' ),
	)
);

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-wishlist-grid sm-wishlist-grid--cols-' . $sm_columns )
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/wishlist"
	data-wp-init="callbacks.loadGrid"
>
	<p class="sm-wishlist-grid__empty" data-wp-bind--hidden="state.hasProducts">
		<?php echo esc_html__( 'You have not saved anything yet.', 'suitemart' ); ?>
	</p>

	<p class="sm-wishlist-grid__status" role="status" data-wp-text="state.statusText"></p>

	<ul class="sm-wishlist-grid__list" data-wp-bind--hidden="state.isEmpty">
		<template
			data-wp-each--product="state.products"
			data-wp-each-key="context.product.id"
		>
			<li class="sm-wishlist-grid__item">
				<a
					class="sm-wishlist-grid__link"
					data-wp-bind--href="context.product.permalink"
				>
					<img
						class="sm-wishlist-grid__image"
						data-wp-bind--src="context.product.image"
						data-wp-bind--alt="context.product.imageAlt"
						data-wp-bind--hidden="!context.product.image"
						width="300"
						height="300"
						loading="lazy"
						decoding="async"
					/>
					<span
						class="sm-wishlist-grid__name"
						data-wp-text="context.product.name"
					></span>
				</a>
				<?php if ( $sm_show_price ) : ?>
					<p class="sm-wishlist-grid__price" data-wp-text="context.product.price"></p>
				<?php endif; ?>
				<?php if ( $sm_show_stock ) : ?>
					<p class="sm-wishlist-grid__stock" data-wp-text="context.product.stock"></p>
				<?php endif; ?>
				<button
					type="button"
					class="sm-wishlist-grid__remove"
					data-wp-on--click="actions.removeSaved"
					data-wp-bind--aria-label="context.product.removeLabel"
				>
					<?php echo suitemart_get_icon( 'trash', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
				</button>
			</li>
		</template>
	</ul>
</div>
