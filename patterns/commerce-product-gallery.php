<?php
/**
 * Title: Product gallery with thumbnails
 * Slug: suitemart/commerce-product-gallery
 * Categories: suitemart/commerce, woocommerce
 * Description: A two-column product header using Suitemart's gallery, for products without variations.
 * Keywords: gallery, images, thumbnails, product, slider
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! suitemart_has_woocommerce() ) {
	return;
}

/*
 * This gallery does not yet swap the main image when a variation is chosen, so
 * the single-product template keeps WooCommerce's own gallery block. Reach for
 * this one on simple products, where its thumbnail layouts are the point.
 */

?>
<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"55%"} -->
<div class="wp-block-column" style="flex-basis:55%"><!-- wp:suitemart/product-gallery {"layout":"horizontal"} /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:post-title {"level":1,"fontSize":"2xl"} /-->

<!-- wp:woocommerce/product-price /-->

<!-- wp:woocommerce/product-summary /-->

<!-- wp:woocommerce/add-to-cart-with-options /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
