<?php
/**
 * Title: Deal of the day
 * Slug: suitemart/commerce-deal-of-the-day
 * Categories: suitemart/commerce, woocommerce
 * Description: One discounted product beside a countdown and the stock left on it.
 * Keywords: deal, offer, countdown, timer, scarcity, sale
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
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"var:custom|radius|lg"}},"backgroundColor":"neutral-100","layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide has-neutral-100-background-color has-background" style="border-radius:var(--wp--custom--radius--lg);padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--50)"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"38%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:38%"><!-- wp:paragraph {"textColor":"error","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.1em","fontWeight":"600"}},"fontSize":"sm"} -->
<p class="has-error-color has-text-color has-sm-font-size" style="font-weight:600;text-transform:uppercase;letter-spacing:0.1em"><?php echo esc_html_x( 'Deal of the day', 'Pattern eyebrow text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontSize":"2xl"} -->
<h2 class="wp-block-heading has-2-xl-font-size"><?php echo esc_html_x( 'One price, until midnight', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"neutral-700"} -->
<p class="has-neutral-700-color has-text-color"><?php echo esc_html_x( 'A different product every day, at the lowest price we can hold.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<?php
/*
 * A date written into a pattern file is already in the past by the time anyone
 * inserts it, so this one is computed at insertion and stored as a plain value
 * from then on. Twenty-four hours is what the heading promises.
 */
?>
<!-- wp:suitemart/countdown {"layout":"boxed","units":["hours","minutes","seconds"],"endDate":"<?php echo esc_attr( wp_date( 'Y-m-d\TH:i', strtotime( '+1 day' ) ) ); ?>","expiredText":"<?php echo esc_attr_x( 'Today’s deal has ended.', 'Pattern countdown expiry text', 'suitemart' ); ?>","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} /--></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:woocommerce/product-collection {"queryId":36,"query":{"isProductCollectionBlock":true,"perPage":1,"pages":1,"offset":0,"postType":"product","order":"desc","orderBy":"date","inherit":false,"woocommerceOnSale":true},"queryContextIncludes":["collection"],"displayLayout":{"type":"flex","columns":1},"queryContextIncludes":["collection"],"collection":"woocommerce/product-collection/on-sale"} -->
<div class="wp-block-woocommerce-product-collection"><!-- wp:woocommerce/product-template -->
<!-- wp:woocommerce/product-image {"isDescendentOfQueryLoop":true,"imageSizing":"single","style":{"border":{"radius":"8px"}}} /-->

<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"md","__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"fontSize":"lg"} /-->

<!-- wp:suitemart/stock-progress-bar /-->

<!-- wp:woocommerce/product-button /-->
<!-- /wp:woocommerce/product-template -->

<!-- wp:woocommerce/product-collection-no-results -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Put a product on sale to feature it here.', 'Pattern empty state', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:woocommerce/product-collection-no-results --></div>
<!-- /wp:woocommerce/product-collection --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
