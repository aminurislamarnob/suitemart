<?php
/**
 * Title: Product urgency stack
 * Slug: suitemart/commerce-product-urgency
 * Categories: suitemart/commerce, woocommerce
 * Block Types: core/template-part/product-summary
 * Description: Sale countdown, remaining stock and units sold, for the summary column of a product page.
 * Keywords: countdown, stock, scarcity, sold, urgency
 * Viewport Width: 720
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

// Every block here reads the product from `postId` context, so the pattern only
// makes sense inside a product template or query loop.
if ( ! suitemart_has_woocommerce() ) {
	return;
}

?>
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:suitemart/product-countdown {"layout":"inline"} /-->

<!-- wp:suitemart/stock-progress-bar /-->

<!-- wp:suitemart/sold-counter /--></div>
<!-- /wp:group -->
