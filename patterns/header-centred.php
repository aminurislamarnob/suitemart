<?php
/**
 * Title: Header, centred logo over a menu
 * Slug: suitemart/header-centred
 * Categories: suitemart/header
 * Block Types: core/template-part/header
 * Description: A stacked header: utilities in a thin strip, then a centred logo, then the menu.
 * Keywords: header, centred, menu, navigation, stacked
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"sm-header sm-header--centred","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group sm-header sm-header--centred"><!-- wp:group {"align":"full","backgroundColor":"neutral-100","style":{"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull has-neutral-100-background-color has-background" style="padding-top:var(--wp--preset--spacing--10);padding-bottom:var(--wp--preset--spacing--10)"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"textColor":"neutral-700","fontSize":"sm"} -->
<p class="has-neutral-700-color has-text-color has-sm-font-size"><?php echo esc_html_x( 'Free delivery over £50 · 30-day returns', 'Pattern utility text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"textColor":"neutral-700","fontSize":"sm"} -->
<p class="has-neutral-700-color has-text-color has-sm-font-size"><a href="#"><?php echo esc_html_x( 'Track an order', 'Pattern utility link', 'suitemart' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|30"},"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:site-logo {"width":48} /-->

<!-- wp:site-title {"level":0,"textAlign":"center","fontSize":"xl"} /--></div>
<!-- /wp:group -->

<?php
/*
 * The menu sits under the logo, so it is the row that has to survive a narrow
 * screen. Its own mobile breakpoint turns it into a drawer trigger; nothing
 * above needs to change.
 */
?>
<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"center","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:suitemart/navigation {"mobileBreakpoint":"md","ariaLabel":"<?php echo esc_attr_x( 'Main navigation', 'Pattern label', 'suitemart' ); ?>"} -->
<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Shop', 'Pattern menu item', 'suitemart' ); ?>","url":"/shop"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Collections', 'Pattern menu item', 'suitemart' ); ?>","url":"#"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'About', 'Pattern menu item', 'suitemart' ); ?>","url":"/about"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Contact', 'Pattern menu item', 'suitemart' ); ?>","url":"/contact"} /-->
<!-- /wp:suitemart/navigation --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:separator {"align":"full","backgroundColor":"neutral-200","className":"is-style-wide"} -->
<hr class="wp-block-separator alignfull has-text-color has-neutral-200-color has-alpha-channel-opacity has-neutral-200-background-color has-background is-style-wide"/>
<!-- /wp:separator --></div>
<!-- /wp:group -->
