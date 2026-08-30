<?php
/**
 * Title: Top rated
 * Slug: suitemart/commerce-top-rated
 * Categories: suitemart/commerce, woocommerce
 * Description: The best-reviewed products, three across, with stars and review counts.
 * Keywords: rating, reviews, stars, top rated, best
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
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"textAlign":"center","level":2,"fontSize":"2xl"} -->
<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size"><?php echo esc_html_x( 'Rated highest by customers', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:woocommerce/product-collection {"queryId":35,"query":{"isProductCollectionBlock":true,"perPage":3,"pages":1,"offset":0,"postType":"product","order":"desc","orderBy":"rating","inherit":false},"queryContextIncludes":["collection"],"displayLayout":{"type":"flex","columns":3},"collection":"woocommerce/product-collection/top-rated"} -->
<div class="wp-block-woocommerce-product-collection"><!-- wp:woocommerce/product-template -->
<!-- wp:woocommerce/product-image {"isDescendentOfQueryLoop":true,"imageSizing":"single","style":{"border":{"radius":"8px"}}} /-->

<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"lg","__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->

<?php
/*
 * Stars and the review count are two blocks rather than `product-rating`,
 * because the count is what makes the stars mean anything and this section is
 * about the reviews rather than about the product.
 */
?>
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:woocommerce/product-rating-stars /-->

<!-- wp:woocommerce/product-rating-counter {"fontSize":"sm"} /--></div>
<!-- /wp:group -->

<!-- wp:woocommerce/product-summary {"isDescendentOfQueryLoop":true,"summaryLength":140,"fontSize":"sm"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true} /-->

<!-- wp:woocommerce/product-button {"fontSize":"sm"} /-->
<!-- /wp:woocommerce/product-template -->

<!-- wp:woocommerce/product-collection-no-results -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'No products have been reviewed yet.', 'Pattern empty state', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:woocommerce/product-collection-no-results --></div>
<!-- /wp:woocommerce/product-collection --></div>
<!-- /wp:group -->
