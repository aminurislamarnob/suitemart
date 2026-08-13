<?php
/**
 * Title: Gift cards
 * Slug: suitemart/cta-gift-card
 * Categories: suitemart/cta, call-to-action, banner
 * Description: A gift-card prompt for the run-up to a holiday, with the amounts spelled out.
 * Keywords: gift card, voucher, present, gifting, christmas, birthday
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"align":"wide","verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center">
<?php
/*
 * The card itself is a dark panel rather than an illustration: a shipped
 * graphic would carry Suitemart's own look into every buyer's shop, and this
 * is the one element they will certainly want to replace with their artwork.
 */
?>
<!-- wp:column {"verticalAlignment":"center","width":"45%","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","right":"var:preset|spacing|50","bottom":"var:preset|spacing|70","left":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|20"},"border":{"radius":"var:custom|radius|lg"}},"backgroundColor":"contrast"} -->
<div class="wp-block-column is-vertically-aligned-center has-contrast-background-color has-background" style="border-radius:var(--wp--custom--radius--lg);flex-basis:45%;padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--50)"><!-- wp:paragraph {"textColor":"base","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.12em","fontWeight":"600"}},"fontSize":"sm"} -->
<p class="has-base-color has-text-color has-sm-font-size" style="font-weight:600;text-transform:uppercase;letter-spacing:0.12em"><?php echo esc_html_x( 'Gift card', 'Pattern label', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"textColor":"base","fontSize":"3xl"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-3-xl-font-size"><?php echo esc_html_x( '£50', 'Pattern gift card amount', 'suitemart' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"base","fontSize":"sm"} -->
<p class="has-base-color has-text-color has-sm-font-size"><?php echo esc_html_x( 'Replace this panel with your own card artwork.', 'Pattern placeholder text', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"level":2,"fontSize":"2xl"} -->
<h2 class="wp-block-heading has-2-xl-font-size"><?php echo esc_html_x( 'When you would rather let them choose', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"neutral-700"} -->
<p class="has-neutral-700-color has-text-color"><?php echo esc_html_x( 'Sent by email within the hour, or printed and posted if you would like something to hand over. Valid for two years, and they can spend it in the shop as well as online.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20","margin":{"top":"var:preset|spacing|40"}}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--40)"><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html_x( '£25', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html_x( '£50', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html_x( '£100', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/shop"><?php echo esc_html_x( 'Buy a gift card', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
