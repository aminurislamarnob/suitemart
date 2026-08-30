<?php
/**
 * Title: Shop with a filter sidebar
 * Slug: suitemart/commerce-shop-filters
 * Categories: suitemart/commerce, woocommerce
 * Description: A filter column — price, availability, rating — beside a paginated product grid.
 * Keywords: filters, sidebar, shop, archive, price, rating
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
<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"},"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|70"}}}} -->
<div class="wp-block-columns alignwide" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:column {"width":"260px"} -->
<div class="wp-block-column" style="flex-basis:260px"><!-- wp:woocommerce/product-filters -->
<div class="wp-block-woocommerce-product-filters wc-block-product-filters">
<?php
/*
 * The active filters and the clear button come first on purpose: a shopper who
 * has narrowed too far needs the way out before they need another control, and
 * on a narrow screen everything below the fold is invisible to them.
 *
 * `product-filter-removable-chips` belongs in here and is deliberately absent:
 * its render reads a `$classes` it only assigns when the saved inner markup
 * carries `wc-block-product-filter-removable-chips`, so inserting it from a
 * hand-written pattern raises "Undefined variable $classes" from WooCommerce
 * itself. Add it from the editor, where Woo writes its own markup, or wait for
 * the upstream fix — do not guess at the markup here.
 */
?>
<!-- wp:woocommerce/product-filter-active --><div class="wp-block-woocommerce-product-filter-active"></div><!-- /wp:woocommerce/product-filter-active -->

<!-- wp:woocommerce/product-filter-clear-button /-->

<!-- wp:woocommerce/product-filter-price -->
<div class="wp-block-woocommerce-product-filter-price"><!-- wp:woocommerce/product-filter-price-slider {"showInputFields":true} --><div class="wp-block-woocommerce-product-filter-price-slider wc-block-product-filter-price-slider"></div><!-- /wp:woocommerce/product-filter-price-slider --></div>
<!-- /wp:woocommerce/product-filter-price -->

<!-- wp:woocommerce/product-filter-status {"showCounts":true} --><div class="wp-block-woocommerce-product-filter-status"></div><!-- /wp:woocommerce/product-filter-status -->

<!-- wp:woocommerce/product-filter-rating {"showCounts":true} --><div class="wp-block-woocommerce-product-filter-rating"></div><!-- /wp:woocommerce/product-filter-rating --></div>
<!-- /wp:woocommerce/product-filters --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:woocommerce/product-collection {"queryId":37,"query":{"isProductCollectionBlock":true,"perPage":12,"pages":0,"offset":0,"postType":"product","order":"asc","orderBy":"title","inherit":false},"queryContextIncludes":["collection"],"className":"sm-product-grid","displayLayout":{"type":"flex","columns":3},"collection":"woocommerce/product-collection/product-catalog"} -->
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

<!-- wp:query-pagination {"style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:woocommerce/product-collection-no-results -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Nothing matches those filters. Try widening one of them.', 'Pattern empty state', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:woocommerce/product-collection-no-results --></div>
<!-- /wp:woocommerce/product-collection --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
