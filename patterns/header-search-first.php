<?php
/**
 * Title: Header, search first
 * Slug: suitemart/header-search-first
 * Categories: suitemart/header
 * Block Types: core/template-part/header
 * Description: A wide search field between the logo and the cart, for catalogues people search rather than browse.
 * Keywords: header, search, marketplace, catalogue, cart
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
<!-- wp:group {"className":"sm-header sm-header--search-first","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"},"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group sm-header sm-header--search-first" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:site-logo {"width":36} /-->

<!-- wp:site-title {"level":0} /--></div>
<!-- /wp:group -->

<?php
/*
 * The search field is the widest thing in the row and the first to be squeezed,
 * so it is given the flex-basis rather than left to fight the logo for it.
 */
?>
<!-- wp:group {"style":{"layout":{"selfStretch":"fill","flexSize":null}},"layout":{"type":"constrained","justifyContent":"center"}} -->
<div class="wp-block-group"><!-- wp:suitemart/ajax-search {"postType":"product","placeholder":"<?php echo esc_attr_x( 'Search the whole catalogue…', 'Pattern placeholder', 'suitemart' ); ?>","buttonText":"<?php echo esc_attr_x( 'Search', 'Pattern button text', 'suitemart' ); ?>","resultLimit":8} /--></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:woocommerce/customer-account {"displayStyle":"icon_and_text"} /-->

<!-- wp:woocommerce/mini-cart /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:suitemart/navigation {"mobileBreakpoint":"lg","ariaLabel":"<?php echo esc_attr_x( 'Department navigation', 'Pattern label', 'suitemart' ); ?>"} -->
<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'All departments', 'Pattern menu item', 'suitemart' ); ?>","hasPanel":true} -->
<!-- wp:suitemart/mega-panel {"panelWidth":"wide"} -->
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"fontSize":"md"} -->
<h3 class="wp-block-heading has-md-font-size"><?php echo esc_html_x( 'Home', 'Pattern heading', 'suitemart' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-none","fontSize":"sm"} -->
<ul class="wp-block-list is-style-none has-sm-font-size"><!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Kitchen', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Storage', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Lighting', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"fontSize":"md"} -->
<h3 class="wp-block-heading has-md-font-size"><?php echo esc_html_x( 'Electronics', 'Pattern heading', 'suitemart' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-none","fontSize":"sm"} -->
<ul class="wp-block-list is-style-none has-sm-font-size"><!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Audio', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Wearables', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Accessories', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"fontSize":"md"} -->
<h3 class="wp-block-heading has-md-font-size"><?php echo esc_html_x( 'Outdoor', 'Pattern heading', 'suitemart' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-none","fontSize":"sm"} -->
<ul class="wp-block-list is-style-none has-sm-font-size"><!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Garden', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Camping', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:suitemart/mega-panel -->
<!-- /wp:suitemart/nav-item -->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Deals', 'Pattern menu item', 'suitemart' ); ?>","url":"#"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Brands', 'Pattern menu item', 'suitemart' ); ?>","url":"#"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Help', 'Pattern menu item', 'suitemart' ); ?>","url":"#"} /-->
<!-- /wp:suitemart/navigation --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
