<?php
/**
 * Title: Header, with a countdown strip
 * Slug: suitemart/header-announcement
 * Categories: suitemart/header
 * Block Types: core/template-part/header
 * Description: A promotional strip counting down above the main header bar.
 * Keywords: header, announcement, countdown, promotion, sale
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"sm-header sm-header--announcement","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group sm-header sm-header--announcement">
<?php
/*
 * The strip is set on `contrast` rather than on `primary`: it sits directly
 * above the header and a saturated band there competes with the first
 * call to action on the page, whichever variation is active.
 */
?>
<!-- wp:group {"align":"full","backgroundColor":"contrast","textColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"},"blockGap":"var:preset|spacing|30"},"elements":{"link":{"color":{"text":"var:preset|color|base"}}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"center","verticalAlignment":"center"}} -->
<div class="wp-block-group alignfull has-base-color has-contrast-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20)"><!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><?php echo esc_html_x( 'Mid-season sale ends in', 'Pattern announcement text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<?php
/*
 * A countdown with no date renders nothing on the front end, so the pattern
 * has to supply one — and a date written into the file would be in the past
 * by the time anyone inserted it. This is evaluated when the pattern is
 * inserted, which reads as "a week from now" and is then a real stored value
 * the merchant edits like any other.
 */
?>
<!-- wp:suitemart/countdown {"layout":"inline","units":["days","hours","minutes"],"endDate":"<?php echo esc_attr( wp_date( 'Y-m-d\TH:i', strtotime( '+7 days' ) ) ); ?>","fontSize":"sm"} /-->

<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><a href="#"><?php echo esc_html_x( 'Shop the sale', 'Pattern link text', 'suitemart' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:site-logo {"width":40} /-->

<!-- wp:site-title {"level":0} /--></div>
<!-- /wp:group -->

<!-- wp:suitemart/navigation {"mobileBreakpoint":"lg","ariaLabel":"<?php echo esc_attr_x( 'Main navigation', 'Pattern label', 'suitemart' ); ?>"} -->
<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Shop', 'Pattern menu item', 'suitemart' ); ?>","url":"/shop"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Sale', 'Pattern menu item', 'suitemart' ); ?>","url":"#"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Stockists', 'Pattern menu item', 'suitemart' ); ?>","url":"#"} /-->

<!-- wp:suitemart/nav-item {"label":"<?php echo esc_attr_x( 'Help', 'Pattern menu item', 'suitemart' ); ?>","url":"#"} /-->
<!-- /wp:suitemart/navigation --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
