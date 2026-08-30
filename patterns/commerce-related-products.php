<?php
/**
 * Title: Related products
 * Slug: suitemart/commerce-related-products
 * Categories: suitemart/commerce, woocommerce
 * Description: Four products related to the one being viewed, for the foot of a product page.
 * Keywords: related, similar, cross-sell, you may also like
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
<?php
/*
 * `woocommerce/related-products` is a wrapper that hides itself when the
 * product has no relations, which is why the heading lives inside it rather
 * than above it — put the heading outside and an unrelated product leaves a
 * title with nothing under it.
 */
?>
<!-- wp:woocommerce/related-products {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|70"}}}} -->
<div class="wp-block-woocommerce-related-products alignwide" style="margin-top:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":2,"fontSize":"xl"} -->
<h2 class="wp-block-heading has-xl-font-size"><?php echo esc_html_x( 'You may also like', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:woocommerce/product-collection {"queryId":38,"query":{"isProductCollectionBlock":true,"perPage":4,"pages":1,"offset":0,"postType":"product","order":"asc","orderBy":"title","inherit":false,"woocommerceStockStatus":["instock","onbackorder"]},"queryContextIncludes":["collection"],"className":"sm-product-grid","displayLayout":{"type":"flex","columns":4},"collection":"woocommerce/product-collection/related"} -->
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
<!-- /wp:woocommerce/product-template --></div>
<!-- /wp:woocommerce/product-collection --></div>
<!-- /wp:woocommerce/related-products -->
