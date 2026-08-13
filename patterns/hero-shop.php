<?php
/**
 * Title: Shop hero
 * Slug: suitemart/hero-shop
 * Categories: suitemart/hero, banner
 * Description: A full-width hero with a headline, supporting text and two calls to action.
 * Keywords: hero, banner, header, promotion
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"full","backgroundColor":"neutral-100","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull has-neutral-100-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained","contentSize":"640px"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"align":"center","textColor":"primary","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.08em","fontWeight":"600"}},"fontSize":"sm"} -->
<p class="has-text-align-center has-primary-color has-text-color has-sm-font-size" style="font-weight:600;text-transform:uppercase;letter-spacing:0.08em"><?php echo esc_html_x( 'New season', 'Pattern eyebrow text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":1,"fontSize":"3xl"} -->
<h1 class="wp-block-heading has-text-align-center has-3-xl-font-size"><?php echo esc_html_x( 'Everything your store needs, nothing it does not', 'Pattern headline', 'suitemart' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"neutral-600","fontSize":"lg"} -->
<p class="has-text-align-center has-neutral-600-color has-text-color has-lg-font-size"><?php echo esc_html_x( 'Built entirely on the Site Editor, so every colour, layout and section is yours to change without touching code.', 'Pattern supporting text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--20)"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html_x( 'Shop new arrivals', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html_x( 'Browse the catalogue', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
