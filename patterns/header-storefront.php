<?php
/**
 * Title: Header, utility strip, contact details and centred menu
 * Slug: suitemart/header-storefront
 * Categories: suitemart/header
 * Block Types: core/template-part/header
 * Description: A three-row storefront header: a coloured utility strip with language and region menus, social links and help links; phone and email beside a centred logo with account, search, wishlist and cart; then a centred uppercase menu.
 * Keywords: header, storefront, utility bar, top bar, contact, wishlist, cart, menu
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

// The cart, account and wishlist blocks need WooCommerce, and a pattern that
// names an unregistered block inserts as an error rather than as a header.
if ( ! suitemart_has_woocommerce() ) {
	return;
}

?>
<!-- wp:group {"className":"sm-header sm-header--storefront","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group sm-header sm-header--storefront">
<?php
/*
 * The strip is `base` on `primary`, the same pair the theme's buttons use, so
 * every style variation has already had to make it pass AA — see the pair
 * list in tests/phpunit/test-style-variations.php.
 *
 * The language and region menus are a navigation block so that they open as
 * dropdowns on desktop and fold into a drawer on a phone. Their panels state
 * their own text and link colour because they inherit the strip's `base`
 * otherwise — white on the panel's white.
 */
?>
<!-- wp:group {"align":"full","className":"sm-header__utility","backgroundColor":"primary","textColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10"}},"elements":{"link":{"color":{"text":"var:preset|color|base"}}},"typography":{"textTransform":"uppercase","letterSpacing":"0.04em"}},"fontSize":"sm","layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull sm-header__utility has-base-color has-primary-background-color has-text-color has-background has-link-color has-sm-font-size" style="padding-top:var(--wp--preset--spacing--10);padding-bottom:var(--wp--preset--spacing--10);text-transform:uppercase;letter-spacing:0.04em"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:suitemart/navigation {"mobileBreakpoint":"sm","textColor":"base","ariaLabel":"<?php echo esc_attr_x( 'Language and region', 'Pattern label', 'suitemart' ); ?>","style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'English', 'Pattern menu item', 'suitemart' ); ?>","hasPanel":true} -->
<!-- wp:suitemart/mega-panel {"panelWidth":"auto","textColor":"contrast","style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}}} -->
<!-- wp:list {"className":"is-style-none"} -->
<ul class="wp-block-list is-style-none"><!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'English', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Français', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Deutsch', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Español', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->
<!-- /wp:suitemart/mega-panel -->
<!-- /wp:suitemart/nav-item -->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Country', 'Pattern menu item', 'suitemart' ); ?>","hasPanel":true} -->
<!-- wp:suitemart/mega-panel {"panelWidth":"auto","textColor":"contrast","style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}}} -->
<!-- wp:list {"className":"is-style-none"} -->
<ul class="wp-block-list is-style-none"><!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'United Kingdom', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'United States', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Germany', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'France', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->
<!-- /wp:suitemart/mega-panel -->
<!-- /wp:suitemart/nav-item -->
<!-- /wp:suitemart/navigation -->

<!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}}} -->
<p style="font-weight:600"><?php echo esc_html_x( 'Free shipping for all orders of $150', 'Pattern utility text', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:social-links {"iconColor":"base","iconColorValue":"var(--wp--preset--color--base)","size":"has-small-icon-size","className":"is-style-logos-only"} -->
<ul class="wp-block-social-links has-small-icon-size has-icon-color is-style-logos-only"><!-- wp:social-link {"url":"#","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"x"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /-->

<!-- wp:social-link {"url":"#","service":"youtube"} /-->

<!-- wp:social-link {"url":"#","service":"pinterest"} /--></ul>
<!-- /wp:social-links -->

<?php
/*
 * The help links are a navigation block rather than paragraphs for the same
 * reason the language menus are: a nav item draws a menu link, a paragraph
 * draws a body link, and the strip should read as one row.
 */
?>
<!-- wp:suitemart/navigation {"mobileBreakpoint":"sm","textColor":"base","ariaLabel":"<?php echo esc_attr_x( 'Help', 'Pattern label', 'suitemart' ); ?>","style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Newsletter', 'Pattern utility link', 'suitemart' ); ?>","url":"#"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Contact us', 'Pattern utility link', 'suitemart' ); ?>","url":"/contact"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'FAQs', 'Pattern utility link', 'suitemart' ); ?>","url":"#"} /-->
<!-- /wp:suitemart/navigation --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<?php
/*
 * The contact cluster is hidden below the menu breakpoint by its class (see
 * `.sm-header__contact` in src/_shared/_appearance.scss), so on a phone this
 * row is the logo and the actions and nothing else.
 */
?>
<!-- wp:group {"className":"sm-header__main","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group sm-header__main" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"className":"sm-header__contact","style":{"spacing":{"blockGap":"var:preset|spacing|50"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
<div class="wp-block-group sm-header__contact"><!-- wp:suitemart/infobox {"icon":"phone","iconSize":28,"title":"<?php echo esc_attr_x( 'Call toll-free', 'Pattern contact label', 'suitemart' ); ?>","titleLevel":5,"description":"<?php echo esc_attr_x( '+73 099 321 312', 'Pattern phone number', 'suitemart' ); ?>","url":"tel:+73099321312","orientation":"horizontal","alignment":"start","fontSize":"sm"} /-->

<!-- wp:suitemart/infobox {"icon":"mail","iconSize":28,"title":"<?php echo esc_attr_x( 'Any questions', 'Pattern contact label', 'suitemart' ); ?>","titleLevel":5,"description":"hello@example.com","url":"mailto:hello@example.com","orientation":"horizontal","alignment":"start","fontSize":"sm"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"sm-header__brand","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center","verticalAlignment":"center"}} -->
<div class="wp-block-group sm-header__brand"><!-- wp:site-logo {"width":40} /-->

<!-- wp:site-title {"level":0,"fontSize":"xl"} /--></div>
<!-- /wp:group -->

<?php
/*
 * Search and wishlist are icon-only triggers for the two off-canvas panels at
 * the end of the header; the trigger's own border and padding are reset so
 * the four controls read as one row of marks rather than two buttons among
 * two links. Each trigger is paired with its panel by `panelId` and by
 * nothing else.
 */
?>
<!-- wp:group {"className":"sm-header__actions","style":{"spacing":{"blockGap":"var:preset|spacing|20"},"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
<div class="wp-block-group sm-header__actions has-link-color"><!-- wp:woocommerce/customer-account {"displayStyle":"text_only"} /-->

<!-- wp:suitemart/off-canvas-trigger {"panelId":"header-search","label":"<?php echo esc_attr_x( 'Search', 'Pattern button text', 'suitemart' ); ?>","icon":"search","showLabel":false,"style":{"border":{"width":"0px"},"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10","left":"var:preset|spacing|10","right":"var:preset|spacing|10"}}}} /-->

<!-- wp:suitemart/off-canvas-trigger {"panelId":"header-wishlist","label":"<?php echo esc_attr_x( 'Wishlist', 'Pattern button text', 'suitemart' ); ?>","icon":"heart","showLabel":false,"style":{"border":{"width":"0px"},"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10","left":"var:preset|spacing|10","right":"var:preset|spacing|10"}}}} /-->

<!-- wp:woocommerce/mini-cart {"hasHiddenPrice":false,"productCountVisibility":"always"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<?php
/*
 * The menu is centred, so its mega panel is `full`: a `wide` panel anchors to
 * its own item's left edge and would run off the right of the viewport from
 * the middle of the row. The inner group re-centres the columns at wide size.
 */
?>
<!-- wp:group {"className":"sm-header__menu","style":{"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10"}},"border":{"bottom":{"color":"var:preset|color|neutral-200","style":"solid","width":"1px"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group sm-header__menu" style="border-bottom-color:var(--wp--preset--color--neutral-200);border-bottom-style:solid;border-bottom-width:1px;padding-top:var(--wp--preset--spacing--10);padding-bottom:var(--wp--preset--spacing--10)"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"center","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:suitemart/navigation {"mobileBreakpoint":"lg","ariaLabel":"<?php echo esc_attr_x( 'Main navigation', 'Pattern label', 'suitemart' ); ?>","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.04em"},"spacing":{"blockGap":"var:preset|spacing|30"}},"fontSize":"sm"} -->
<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Home', 'Pattern menu item', 'suitemart' ); ?>","url":"/"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Shop', 'Pattern menu item', 'suitemart' ); ?>","hasPanel":true} -->
<!-- wp:suitemart/mega-panel {"panelWidth":"full"} -->
<!-- wp:group {"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group"><!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"fontSize":"md"} -->
<h3 class="wp-block-heading has-md-font-size"><?php echo esc_html_x( 'Categories', 'Pattern heading', 'suitemart' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-none"} -->
<ul class="wp-block-list is-style-none"><!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'New arrivals', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Best sellers', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Sale', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"fontSize":"md"} -->
<h3 class="wp-block-heading has-md-font-size"><?php echo esc_html_x( 'Collections', 'Pattern heading', 'suitemart' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-none"} -->
<ul class="wp-block-list is-style-none"><!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Featured', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Seasonal', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:group {"backgroundColor":"neutral-100","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"8px"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-neutral-100-background-color has-background" style="border-radius:8px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:heading {"level":3,"fontSize":"lg"} -->
<h3 class="wp-block-heading has-lg-font-size"><?php echo esc_html_x( 'Season sale', 'Pattern heading', 'suitemart' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><?php echo esc_html_x( 'Up to 40% off selected lines.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"fontSize":"sm"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-sm-font-size has-custom-font-size wp-element-button" href="#"><?php echo esc_html_x( 'Shop the sale', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
<!-- /wp:suitemart/mega-panel -->
<!-- /wp:suitemart/nav-item -->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Blog', 'Pattern menu item', 'suitemart' ); ?>","url":"/blog"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'About', 'Pattern menu item', 'suitemart' ); ?>","url":"/about"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Contact', 'Pattern menu item', 'suitemart' ); ?>","url":"/contact"} /-->
<!-- /wp:suitemart/navigation --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:suitemart/off-canvas {"panelId":"header-search","side":"top","size":"18rem","title":"<?php echo esc_attr_x( 'Search', 'Pattern panel title', 'suitemart' ); ?>"} -->
<!-- wp:group {"layout":{"type":"constrained","contentSize":"720px"}} -->
<div class="wp-block-group"><!-- wp:suitemart/ajax-search {"postType":"product","placeholder":"<?php echo esc_attr_x( 'Search products…', 'Pattern placeholder', 'suitemart' ); ?>","resultLimit":6} /--></div>
<!-- /wp:group -->
<!-- /wp:suitemart/off-canvas -->

<!-- wp:suitemart/off-canvas {"panelId":"header-wishlist","side":"end","size":"24rem","title":"<?php echo esc_attr_x( 'Wishlist', 'Pattern panel title', 'suitemart' ); ?>"} -->
<!-- wp:suitemart/wishlist-grid {"columns":1} /-->
<!-- /wp:suitemart/off-canvas --></div>
<!-- /wp:group -->
