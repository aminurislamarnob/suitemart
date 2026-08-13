<?php
/**
 * Title: Shop by category
 * Slug: suitemart/commerce-category-list
 * Categories: suitemart/commerce, woocommerce
 * Description: Every product category with its product count, as a browsable list.
 * Keywords: categories, departments, browse, shop by
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
<h2 class="wp-block-heading has-2-xl-font-size"><?php echo esc_html_x( 'Shop by category', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<?php
/*
 * Woo's own block, not a hand-built grid of links: it lists whatever categories
 * the shop actually has, keeps the counts right, and needs no maintenance when
 * the merchant adds one. `hasEmpty` stays off so a category with no products
 * never advertises itself.
 *
 * `hasImage` stays off: the block draws a thumbnail slot per row whether or not
 * the category has an image set, and almost none do — so switching it on ships
 * a column of broken placeholders. Turn it on once the images exist.
 */
?>
<!-- wp:woocommerce/product-categories {"hasCount":true,"hasImage":false,"hasEmpty":false,"isHierarchical":true} /--></div>
<!-- /wp:group -->
