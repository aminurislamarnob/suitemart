<?php
/**
 * Title: Full-width offer
 * Slug: suitemart/cta-offer-cover
 * Categories: suitemart/cta, banner, call-to-action
 * Description: One offer given the full width of the page, centred, with a single button.
 * Keywords: offer, sale, cta, cover, promotion, full width
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:cover {"overlayColor":"primary","isUserOverlayColor":true,"minHeight":420,"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--40);min-height:420px"><span aria-hidden="true" class="wp-block-cover__background has-primary-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","textColor":"base","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.08em","fontWeight":"600"}},"fontSize":"sm"} -->
<p class="has-text-align-center has-base-color has-text-color has-sm-font-size" style="font-weight:600;text-transform:uppercase;letter-spacing:0.08em"><?php echo esc_html_x( 'Mid-season', 'Pattern eyebrow text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":2,"textColor":"base","fontSize":"3xl"} -->
<h2 class="wp-block-heading has-text-align-center has-base-color has-text-color has-3-xl-font-size"><?php echo esc_html_x( 'Up to forty per cent off', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"base","fontSize":"lg"} -->
<p class="has-text-align-center has-base-color has-text-color has-lg-font-size"><?php echo esc_html_x( 'On last season, while it lasts. No code needed — the price you see is the price you pay.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<?php
/*
 * A solid overlay colour rather than an image. Shipping a photograph would mean
 * licensing it for redistribution and every buyer inheriting the same picture;
 * the merchant swaps this for their own in one click.
 */
?>
<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)"><!-- wp:button {"backgroundColor":"base","textColor":"primary"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-primary-color has-base-background-color has-text-color has-background wp-element-button" href="/shop"><?php echo esc_html_x( 'Shop the sale', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:cover -->
