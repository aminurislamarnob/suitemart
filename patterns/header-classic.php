<?php
/**
 * Title: Header, logo and menu with shop actions
 * Slug: suitemart/header-classic
 * Categories: suitemart/header
 * Block Types: core/template-part/header
 * Description: Logo on the left, menu in the middle, live search and account and cart on the right.
 * Keywords: header, menu, navigation, cart, search
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

// The cart and account blocks are WooCommerce's, and a pattern that names an
// unregistered block inserts as an error rather than as a header.
if ( ! suitemart_has_woocommerce() ) {
	return;
}

?>
<!-- wp:group {"className":"sm-header","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}},"border":{"bottom":{"color":"var:preset|color|neutral-200","style":"solid","width":"1px"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group sm-header has-border-color" style="border-bottom-color:var(--wp--preset--color--neutral-200);border-bottom-style:solid;border-bottom-width:1px;padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:site-logo {"width":40} /-->

<!-- wp:site-title {"level":0} /--></div>
<!-- /wp:group -->

<!-- wp:suitemart/navigation {"mobileBreakpoint":"lg","ariaLabel":"<?php echo esc_attr_x( 'Main navigation', 'Pattern label', 'suitemart' ); ?>"} -->
<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Shop', 'Pattern menu item', 'suitemart' ); ?>","url":"/shop"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'New in', 'Pattern menu item', 'suitemart' ); ?>","url":"#"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Sale', 'Pattern menu item', 'suitemart' ); ?>","url":"#"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Journal', 'Pattern menu item', 'suitemart' ); ?>","url":"#"} /-->
<!-- /wp:suitemart/navigation -->

<!-- wp:group {"className":"sm-header__actions","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
<div class="wp-block-group sm-header__actions"><!-- wp:suitemart/ajax-search {"postType":"product","placeholder":"<?php echo esc_attr_x( 'Search products…', 'Pattern placeholder', 'suitemart' ); ?>","resultLimit":5} /-->

<!-- wp:woocommerce/customer-account {"displayStyle":"icon_only"} /-->

<!-- wp:woocommerce/mini-cart /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
