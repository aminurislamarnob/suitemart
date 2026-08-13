<?php
/**
 * Title: Header, menu in a drawer
 * Slug: suitemart/header-drawer
 * Categories: suitemart/header
 * Block Types: core/template-part/header
 * Description: A quiet bar whose whole menu lives in a side drawer, at every width.
 * Keywords: header, drawer, off-canvas, menu, minimal
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"sm-header sm-header--drawer","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group sm-header sm-header--drawer" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide">
<?php
/*
 * The trigger and the panel are paired by `panelId`, and nothing else pairs
 * them: change one and the button opens nothing. Both say `main-menu` here.
 */
?>
<!-- wp:suitemart/off-canvas-trigger {"panelId":"main-menu","icon":"menu","label":"<?php echo esc_attr_x( 'Menu', 'Pattern button text', 'suitemart' ); ?>"} /-->

<!-- wp:site-title {"level":0,"textAlign":"center","fontSize":"lg"} /-->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:suitemart/ajax-search {"placeholder":"<?php echo esc_attr_x( 'Search…', 'Pattern placeholder', 'suitemart' ); ?>","resultLimit":5} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:suitemart/off-canvas {"panelId":"main-menu","side":"start","size":"20rem","title":"<?php echo esc_attr_x( 'Menu', 'Pattern panel title', 'suitemart' ); ?>"} -->
<!-- wp:list {"className":"is-style-none","fontSize":"lg"} -->
<ul class="wp-block-list is-style-none has-lg-font-size"><!-- wp:list-item -->
<li><a href="/shop"><?php echo esc_html_x( 'Shop', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'New in', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Collections', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="/about"><?php echo esc_html_x( 'About', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="/contact"><?php echo esc_html_x( 'Contact', 'Pattern menu item', 'suitemart' ); ?></a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:separator {"backgroundColor":"neutral-200","className":"is-style-wide"} -->
<hr class="wp-block-separator has-text-color has-neutral-200-color has-alpha-channel-opacity has-neutral-200-background-color has-background is-style-wide"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"textColor":"neutral-700","fontSize":"sm"} -->
<p class="has-neutral-700-color has-text-color has-sm-font-size"><?php echo esc_html_x( 'Free delivery over £50, and 30 days to change your mind.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:suitemart/off-canvas --></div>
<!-- /wp:group -->
