<?php
/**
 * Title: Wishlist page
 * Slug: suitemart/commerce-wishlist-page
 * Categories: suitemart/commerce, woocommerce
 * Description: The saved-items page — a heading, the wishlist grid, and a route back to the shop.
 * Keywords: wishlist, saved, favourites, list
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
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|70"},"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":1,"fontSize":"2xl"} -->
<h1 class="wp-block-heading has-2-xl-font-size"><?php echo esc_html_x( 'Your wishlist', 'Pattern heading', 'suitemart' ); ?></h1>
<!-- /wp:heading -->

<?php
/*
 * The list lives in this browser's localStorage, not in an account, so it is
 * worth saying so — otherwise an empty grid on a second device reads as a bug.
 */
?>
<!-- wp:paragraph {"textColor":"neutral-700"} -->
<p class="has-neutral-700-color has-text-color"><?php echo esc_html_x( 'Saved on this device. Clearing your browser data clears the list.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:suitemart/wishlist-grid {"columns":4,"showPrice":true,"showStock":true} /-->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--20)"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/shop"><?php echo esc_html_x( 'Keep shopping', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
