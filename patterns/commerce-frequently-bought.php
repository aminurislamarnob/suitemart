<?php
/**
 * Title: Frequently bought together
 * Slug: suitemart/commerce-frequently-bought
 * Categories: suitemart/commerce, woocommerce
 * Description: A cross-sell bundle with a combined total, below the product details.
 * Keywords: cross-sell, bundle, upsell, together, cart
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
<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|70"},"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":2,"fontSize":"xl"} -->
<h2 class="wp-block-heading has-xl-font-size"><?php echo esc_html_x( 'Frequently bought together', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><?php echo esc_html_x( 'Built from the cross-sells set on the product. Untick anything you do not want before adding the bundle.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:suitemart/fbt-products /--></div>
<!-- /wp:group -->
