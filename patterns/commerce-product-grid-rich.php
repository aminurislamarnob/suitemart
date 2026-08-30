<?php
/**
 * Title: Product grid with card actions
 * Slug: suitemart/commerce-product-grid-rich
 * Categories: suitemart/commerce, woocommerce
 * Description: A product grid whose cards carry labels, rating, wishlist, compare and quick view.
 * Keywords: products, grid, wishlist, compare, quick view, rating
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! suitemart_has_woocommerce() ) {
	return;
}

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"},"blockGap":"var:preset|spacing|50"}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":2,"fontSize":"2xl"} -->
<h2 class="wp-block-heading has-2-xl-font-size"><?php echo esc_html_x( 'The full range', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:woocommerce/product-collection {"queryId":31,"query":{"isProductCollectionBlock":true,"perPage":8,"pages":1,"offset":0,"postType":"product","order":"desc","orderBy":"date","inherit":false},"queryContextIncludes":["collection"],"className":"sm-product-grid","displayLayout":{"type":"flex","columns":4},"collection":"woocommerce/product-collection/product-catalog"} -->
<div class="wp-block-woocommerce-product-collection sm-product-grid"><!-- wp:woocommerce/product-template -->
<!-- wp:group {"className":"sm-product-card__media","layout":{"type":"default"}} -->
<div class="wp-block-group sm-product-card__media"><!-- wp:suitemart/product-labels /-->

<!-- wp:woocommerce/product-image {"isDescendentOfQueryLoop":true,"imageSizing":"thumbnail","showSaleBadge":false,"scale":"contain","aspectRatio":"1"} /-->

<!-- wp:group {"className":"sm-product-card__actions","style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center","verticalAlignment":"center"}} -->
<div class="wp-block-group sm-product-card__actions"><!-- wp:suitemart/add-to-cart-button {"appearance":"icon","iconSize":22} /-->

<!-- wp:suitemart/quick-view-button {"appearance":"icon","iconSize":22} /-->

<!-- wp:suitemart/compare-button {"appearance":"icon","iconSize":22} /-->

<!-- wp:suitemart/wishlist-button {"appearance":"icon","iconSize":22} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"md","fontFamily":"serif","__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->

<!-- wp:post-terms {"term":"product_cat","className":"sm-product-card__terms","fontSize":"sm"} /-->

<!-- wp:woocommerce/product-rating {"isDescendentOfQueryLoop":true,"fontSize":"sm"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true} /-->
<!-- /wp:woocommerce/product-template -->

<!-- wp:woocommerce/product-collection-no-results -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'No products to show yet.', 'Pattern empty state', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:woocommerce/product-collection-no-results --></div>
<!-- /wp:woocommerce/product-collection --></div>
<!-- /wp:group -->
