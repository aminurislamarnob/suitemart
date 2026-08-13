<?php
/**
 * Title: Hero, offer with a countdown
 * Slug: suitemart/hero-offer-countdown
 * Categories: suitemart/hero, suitemart/cta, banner
 * Description: A single offer with a running countdown beside it, for a sale that genuinely ends.
 * Keywords: hero, countdown, offer, sale, timer
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"full","backgroundColor":"neutral-100","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull has-neutral-100-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"align":"wide","verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%"><!-- wp:heading {"level":1,"fontSize":"3xl"} -->
<h1 class="wp-block-heading has-3-xl-font-size"><?php echo esc_html_x( 'Thirty per cent off everything', 'Pattern headline', 'suitemart' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"neutral-700","fontSize":"lg"} -->
<p class="has-neutral-700-color has-text-color has-lg-font-size"><?php echo esc_html_x( 'No code needed. The discount is already in the price you see.', 'Pattern supporting text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--30)"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/shop"><?php echo esc_html_x( 'Shop the sale', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:group {"backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|30"},"border":{"radius":"var:custom|radius|lg"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-base-background-color has-background" style="border-radius:var(--wp--custom--radius--lg);padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"align":"center","textColor":"neutral-700","fontSize":"sm"} -->
<p class="has-text-align-center has-neutral-700-color has-text-color has-sm-font-size"><?php echo esc_html_x( 'Ends in', 'Pattern label', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<?php
/*
 * Evaluated when the pattern is inserted, not when the file was written — a
 * date typed into the file would already have passed, and a countdown with no
 * date renders nothing at all on the front end.
 */
?>
<!-- wp:suitemart/countdown {"layout":"boxed","endDate":"<?php echo esc_attr( wp_date( 'Y-m-d\TH:i', strtotime( '+3 days' ) ) ); ?>","expiredText":"<?php echo esc_attr_x( 'This sale has ended.', 'Pattern text', 'suitemart' ); ?>"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
