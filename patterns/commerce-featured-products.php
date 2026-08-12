<?php
/**
 * Title: Featured products grid
 * Slug: suitemart/commerce-featured-products
 * Categories: suitemart/commerce, woocommerce
 * Description: A four-column grid of featured products with a section heading.
 * Keywords: products, shop, grid, featured, woocommerce
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

// Woo blocks are unavailable without WooCommerce, and a pattern referencing
// missing blocks renders as an error in the inserter.
if ( ! suitemart_has_woocommerce() ) {
	return;
}

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"},"blockGap":"var:preset|spacing|50"}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":2,"fontSize":"2xl"} -->
<h2 class="wp-block-heading has-2xl-font-size"><?php echo esc_html_x( 'Featured products', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><a href="#"><?php echo esc_html_x( 'View all products', 'Pattern link text', 'suitemart' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:woocommerce/product-collection {"queryId":21,"query":{"perPage":4,"pages":1,"offset":0,"postType":"product","order":"desc","orderBy":"date","inherit":false,"featured":true},"displayLayout":{"type":"flex","columns":4}} -->
<div class="wp-block-woocommerce-product-collection"><!-- wp:woocommerce/product-template -->
<!-- wp:suitemart/product-labels /-->
<!-- wp:woocommerce/product-image {"imageSizing":"thumbnail","style":{"border":{"radius":"8px"}}} /-->

<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"md","__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->

<!-- wp:woocommerce/product-price /-->

<!-- wp:suitemart/quick-view-button {"appearance":"icon"} /-->

<!-- wp:woocommerce/product-button {"fontSize":"sm"} /-->
<!-- /wp:woocommerce/product-template -->

<!-- wp:woocommerce/product-collection-no-results -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'No featured products yet. Mark a product as featured to show it here.', 'Pattern empty state', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:woocommerce/product-collection-no-results --></div>
<!-- /wp:woocommerce/product-collection --></div>
<!-- /wp:group -->
