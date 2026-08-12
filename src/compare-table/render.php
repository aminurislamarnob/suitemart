<?php
/**
 * Compare table block.
 *
 * The comparison list lives in the visitor's browser, so the server has nothing
 * to render: it emits the empty state and the table's scaffolding, and the
 * browser fills the rows in from the Store API once it has read the list.
 *
 * One product per row, not per column.
 *
 * Comparison tables conventionally give each product a column and each
 * attribute a row, which is fine for four products on a desktop and unusable on
 * a phone: the table grows sideways, so it either scrolls horizontally or the
 * columns collapse to a width that fits nothing. Rows scale down instead, and
 * "read down a column to compare" is no harder than reading across. It also
 * means one iteration over the list rather than one per attribute row.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_show_image  = ( $attributes['showImage'] ?? true ) !== false;
$sm_show_rating = ( $attributes['showRating'] ?? true ) !== false;
$sm_show_stock  = ( $attributes['showStock'] ?? true ) !== false;
$sm_show_sku    = ( $attributes['showSku'] ?? false ) === true;

/*
 * Everything the browser needs to render a row, plus everything a binding
 * refers to before the list has loaded. A `data-wp-bind` the server cannot
 * resolve strips its attribute, so `hasProducts` and `isEmpty` are seeded with
 * the only state the server can honestly claim: nothing has loaded yet.
 */
wp_interactivity_state(
	'suitemart/compare',
	array(
		'productsUrl'  => rest_url( 'wc/store/v1/products' ),
		'limit'        => suitemart_compare_limit(),
		'hasProducts'  => false,
		'isEmpty'      => true,
		'isLoading'    => false,
		// Seeded empty rather than left undefined: a `data-wp-text` the server
		// cannot resolve erases the element's contents, and this one is a live
		// region whose emptiness is meaningful.
		'statusText'   => '',
		'emptyText'    => __( 'You have not added anything to compare yet.', 'suitemart' ),
		'errorText'    => __( 'The comparison could not be loaded. Please try again.', 'suitemart' ),
		'removeLabel'  => __( 'Remove from compare', 'suitemart' ),
		'inStockText'  => __( 'In stock', 'suitemart' ),
		'outStockText' => __( 'Out of stock', 'suitemart' ),
		'noRatingText' => __( 'No reviews yet', 'suitemart' ),
	)
);

$sm_wrapper = get_block_wrapper_attributes( array( 'class' => 'sm-compare-table' ) );
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/compare"
	data-wp-init="callbacks.load"
>
	<p class="sm-compare-table__empty" data-wp-bind--hidden="state.hasProducts">
		<?php echo esc_html__( 'You have not added anything to compare yet.', 'suitemart' ); ?>
	</p>

	<?php /* Announced rather than shown: the table appears when it is ready. */ ?>
	<p class="sm-compare-table__status" role="status" data-wp-text="state.statusText"></p>

	<div class="sm-compare-table__scroll" data-wp-bind--hidden="state.isEmpty">
		<table>
			<caption class="screen-reader-text">
				<?php echo esc_html__( 'Products you are comparing', 'suitemart' ); ?>
			</caption>
			<thead>
				<tr>
					<?php if ( $sm_show_image ) : ?>
						<th scope="col">
							<span class="screen-reader-text"><?php echo esc_html__( 'Image', 'suitemart' ); ?></span>
						</th>
					<?php endif; ?>
					<th scope="col"><?php echo esc_html__( 'Product', 'suitemart' ); ?></th>
					<th scope="col"><?php echo esc_html__( 'Price', 'suitemart' ); ?></th>
					<?php if ( $sm_show_rating ) : ?>
						<th scope="col"><?php echo esc_html__( 'Rating', 'suitemart' ); ?></th>
					<?php endif; ?>
					<?php if ( $sm_show_stock ) : ?>
						<th scope="col"><?php echo esc_html__( 'Availability', 'suitemart' ); ?></th>
					<?php endif; ?>
					<?php if ( $sm_show_sku ) : ?>
						<th scope="col"><?php echo esc_html__( 'SKU', 'suitemart' ); ?></th>
					<?php endif; ?>
					<th scope="col">
						<span class="screen-reader-text"><?php echo esc_html__( 'Actions', 'suitemart' ); ?></span>
					</th>
				</tr>
			</thead>
			<tbody>
				<template
					data-wp-each--product="state.products"
					data-wp-each-key="context.product.id"
				>
					<tr>
						<?php if ( $sm_show_image ) : ?>
							<td class="sm-compare-table__image">
								<img
									data-wp-bind--src="context.product.image"
									data-wp-bind--alt="context.product.imageAlt"
									data-wp-bind--hidden="!context.product.image"
									width="96"
									height="96"
									loading="lazy"
									decoding="async"
								/>
							</td>
						<?php endif; ?>
						<th scope="row">
							<a
								data-wp-bind--href="context.product.permalink"
								data-wp-text="context.product.name"
							></a>
						</th>
						<td data-wp-text="context.product.price"></td>
						<?php if ( $sm_show_rating ) : ?>
							<td data-wp-text="context.product.rating"></td>
						<?php endif; ?>
						<?php if ( $sm_show_stock ) : ?>
							<td data-wp-text="context.product.stock"></td>
						<?php endif; ?>
						<?php if ( $sm_show_sku ) : ?>
							<td data-wp-text="context.product.sku"></td>
						<?php endif; ?>
						<td>
							<button
								type="button"
								class="sm-compare-table__remove"
								data-wp-on--click="actions.removeRow"
								data-wp-bind--aria-label="context.product.removeLabel"
							>
								<?php echo suitemart_get_icon( 'x', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
							</button>
						</td>
					</tr>
				</template>
			</tbody>
		</table>
	</div>
</div>
