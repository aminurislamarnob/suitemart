<?php
/**
 * Title: Header, minimal
 * Slug: suitemart/header-minimal
 * Categories: suitemart/header
 * Block Types: core/template-part/header
 * Description: Just a logo and a menu, with no border and no utilities.
 * Keywords: header, minimal, simple, menu, navigation
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"sm-header sm-header--minimal","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group sm-header sm-header--minimal" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:site-title {"level":0,"fontSize":"lg"} /-->

<!-- wp:suitemart/navigation {"mobileBreakpoint":"md","ariaLabel":"<?php echo esc_attr_x( 'Main navigation', 'Pattern label', 'suitemart' ); ?>"} -->
<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Work', 'Pattern menu item', 'suitemart' ); ?>","url":"#"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Studio', 'Pattern menu item', 'suitemart' ); ?>","url":"#"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Contact', 'Pattern menu item', 'suitemart' ); ?>","url":"/contact"} /-->
<!-- /wp:suitemart/navigation --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
