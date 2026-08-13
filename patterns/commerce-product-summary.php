<?php
/**
 * Title: Product summary column
 * Slug: suitemart/commerce-product-summary
 * Categories: suitemart/commerce, woocommerce
 * Description: Title, rating, price, variations, add to cart and the product meta, as one column.
 * Keywords: product, summary, add to cart, variations, price, meta
 * Viewport Width: 720
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! suitemart_has_woocommerce() ) {
	return;
}

?>
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:suitemart/product-labels /-->

<!-- wp:post-title {"level":1,"fontSize":"2xl","__woocommerceNamespace":"woocommerce/product-query/product-title"} /-->

<!-- wp:woocommerce/product-rating /-->

<!-- wp:woocommerce/product-price {"fontSize":"xl"} /-->

<!-- wp:post-excerpt {"__woocommerceNamespace":"woocommerce/product-query/product-summary"} /-->

<?php
/*
 * The three selector blocks inside `add-to-cart-with-options` each render only
 * for the product type they belong to — variable, grouped, everything else — so
 * all three go in and Woo picks. Dropping the ones a demo catalogue does not
 * use is how a theme ships a variable product with no way to choose a variant.
 */
?>
<!-- wp:woocommerce/add-to-cart-with-options {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-woocommerce-add-to-cart-with-options" style="margin-top:var(--wp--preset--spacing--40)"><!-- wp:woocommerce/add-to-cart-with-options-variation-selector /-->

<!-- wp:woocommerce/add-to-cart-with-options-grouped-product-selector /-->

<!-- wp:woocommerce/add-to-cart-with-options-quantity-selector /-->

<!-- wp:woocommerce/product-button /--></div>
<!-- /wp:woocommerce/add-to-cart-with-options -->

<!-- wp:woocommerce/product-stock-indicator {"fontSize":"sm"} /-->

<!-- wp:woocommerce/product-meta {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-woocommerce-product-meta" style="margin-top:var(--wp--preset--spacing--40)"><!-- wp:woocommerce/product-sku /-->

<!-- wp:post-terms {"term":"product_cat","prefix":"<?php echo esc_attr_x( 'Category: ', 'Pattern term prefix', 'suitemart' ); ?>"} /-->

<!-- wp:post-terms {"term":"product_tag","prefix":"<?php echo esc_attr_x( 'Tags: ', 'Pattern term prefix', 'suitemart' ); ?>"} /--></div>
<!-- /wp:woocommerce/product-meta --></div>
<!-- /wp:group -->
