<?php
/**
 * Title: Two promotions side by side
 * Slug: suitemart/cta-promo-pair
 * Categories: suitemart/cta, banner, call-to-action
 * Description: A pair of banners for two campaigns running at once, each with its own link.
 * Keywords: promo, banner, offer, pair, campaign, split
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"grid","columnCount":2,"minimumColumnWidth":"20rem"}} -->
<div class="wp-block-group alignwide">
<?php
/*
 * The banner draws a dark ground of its own when it has no image, so the light
 * text below stays readable in the inserter preview and on the page. Add a
 * photograph and the same scrim sits over that instead.
 */
?>
<!-- wp:suitemart/banner {"url":"/shop","aspectRatio":"16/9","contentPosition":"bottom-left","hoverEffect":"zoom","overlayOpacity":35,"textColor":"base","style":{"border":{"radius":"var:custom|radius|lg"}}} -->
<!-- wp:paragraph {"textColor":"base","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.08em","fontWeight":"600"}},"fontSize":"sm"} -->
<p class="has-base-color has-text-color has-sm-font-size" style="font-weight:600;text-transform:uppercase;letter-spacing:0.08em"><?php echo esc_html_x( 'Ends Sunday', 'Pattern eyebrow text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"textColor":"base","fontSize":"xl"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-xl-font-size"><?php echo esc_html_x( 'Twenty per cent off outerwear', 'Pattern heading', 'suitemart' ); ?></h3>
<!-- /wp:heading -->
<!-- /wp:suitemart/banner -->

<!-- wp:suitemart/banner {"url":"/shop","aspectRatio":"16/9","contentPosition":"bottom-left","hoverEffect":"zoom","overlayOpacity":35,"textColor":"base","style":{"border":{"radius":"var:custom|radius|lg"}}} -->
<!-- wp:paragraph {"textColor":"base","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.08em","fontWeight":"600"}},"fontSize":"sm"} -->
<p class="has-base-color has-text-color has-sm-font-size" style="font-weight:600;text-transform:uppercase;letter-spacing:0.08em"><?php echo esc_html_x( 'Just landed', 'Pattern eyebrow text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"textColor":"base","fontSize":"xl"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-xl-font-size"><?php echo esc_html_x( 'The winter workshop range', 'Pattern heading', 'suitemart' ); ?></h3>
<!-- /wp:heading -->
<!-- /wp:suitemart/banner --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
