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
<div class="wp-block-woocommerce-product-filters">
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
<!-- wp:woocommerce/product-filter-active /-->

<!-- wp:woocommerce/product-filter-clear-button /-->

<!-- wp:woocommerce/product-filter-price -->
<div class="wp-block-woocommerce-product-filter-price"><!-- wp:woocommerce/product-filter-price-slider {"showInputFields":true} /--></div>
<!-- /wp:woocommerce/product-filter-price -->

<!-- wp:woocommerce/product-filter-status {"showCounts":true} /-->

<!-- wp:woocommerce/product-filter-rating {"showCounts":true} /--></div>
<!-- /wp:woocommerce/product-filters --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:woocommerce/product-collection {"queryId":37,"query":{"perPage":12,"pages":0,"offset":0,"postType":"product","order":"asc","orderBy":"title","inherit":false},"displayLayout":{"type":"flex","columns":3},"queryContextIncludes":["collection"],"collection":"woocommerce/product-collection/product-catalog"} -->
<div class="wp-block-woocommerce-product-collection"><!-- wp:woocommerce/product-template -->
<!-- wp:suitemart/product-labels /-->

<!-- wp:woocommerce/product-image {"imageSizing":"thumbnail","showSaleBadge":false,"style":{"border":{"radius":"8px"}}} /-->

<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"md","__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->

<!-- wp:woocommerce/product-price /-->

<!-- wp:woocommerce/product-button {"fontSize":"sm"} /-->
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
