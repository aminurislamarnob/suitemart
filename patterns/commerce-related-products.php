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

<!-- wp:woocommerce/product-collection {"queryId":38,"query":{"perPage":4,"pages":1,"offset":0,"postType":"product","order":"asc","orderBy":"title","inherit":false,"woocommerceStockStatus":["instock","onbackorder"]},"displayLayout":{"type":"flex","columns":4},"collection":"woocommerce/product-collection/related"} -->
<div class="wp-block-woocommerce-product-collection"><!-- wp:woocommerce/product-template -->
<!-- wp:woocommerce/product-image {"imageSizing":"thumbnail","style":{"border":{"radius":"8px"}}} /-->

<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"md","__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->

<!-- wp:woocommerce/product-price /-->

<!-- wp:suitemart/wishlist-button {"appearance":"icon","iconSize":18} /-->
<!-- /wp:woocommerce/product-template --></div>
<!-- /wp:woocommerce/product-collection --></div>
<!-- /wp:woocommerce/related-products -->
