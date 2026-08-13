<?php
/**
 * Title: Product action row
 * Slug: suitemart/commerce-product-actions
 * Categories: suitemart/commerce, woocommerce
 * Description: Wishlist, compare and share, for the summary column beneath add-to-cart.
 * Keywords: wishlist, compare, share, product, actions
 * Viewport Width: 720
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

// Wishlist and compare read the product from `postId` context, so this belongs
// in a product template or a query loop — but both are Suitemart's own blocks
// and only their labels talk about products, so the guard here is about the
// pattern being meaningless without a shop rather than about a missing block.
if ( ! suitemart_has_woocommerce() ) {
	return;
}

?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|20","right":"var:preset|spacing|20"},"blockGap":"var:preset|spacing|30"},"border":{"top":{"width":"1px"},"bottom":{"width":"1px"}}},"borderColor":"neutral-200","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group has-border-color has-neutral-200-border-color" style="border-top-width:1px;border-bottom-width:1px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--20)"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:suitemart/wishlist-button {"appearance":"icon-label"} /-->

<!-- wp:suitemart/compare-button {"appearance":"icon-label"} /--></div>
<!-- /wp:group -->

<!-- wp:suitemart/social-share {"networks":["facebook","x","whatsapp","email","copy"],"shape":"circle","iconSize":18} /--></div>
<!-- /wp:group -->
