<?php
/**
 * Title: Hero, rotating slides
 * Slug: suitemart/hero-slider
 * Categories: suitemart/hero, banner
 * Description: Three full-width slides a visitor can page through, each with its own headline and button.
 * Keywords: hero, slider, carousel, slides, promotion
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * Autoplay is off. A hero that moves on its own takes the page away from
 * someone still reading it, and turning it on is a decision the merchant
 * should make deliberately — WCAG 2.2.2 asks for a pause control when they do.
 */
?>
<!-- wp:suitemart/slider {"align":"full","slidesPerView":1,"slidesPerViewTablet":1,"slidesPerViewDesktop":1,"spaceBetween":0,"loop":true,"showArrows":true,"showPagination":true,"label":"<?php echo esc_attr_x( 'Featured promotions', 'Pattern label', 'suitemart' ); ?>"} -->
<!-- wp:suitemart/slide -->
<!-- wp:cover {"overlayColor":"neutral-100","isUserOverlayColor":true,"minHeight":480,"contentPosition":"center center","layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-cover" style="min-height:480px"><span aria-hidden="true" class="wp-block-cover__background has-neutral-100-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":1,"textColor":"contrast","fontSize":"3xl"} -->
<h1 class="wp-block-heading has-text-align-center has-contrast-color has-text-color has-3-xl-font-size"><?php echo esc_html_x( 'The autumn edit is here', 'Pattern headline', 'suitemart' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"neutral-700","fontSize":"lg"} -->
<p class="has-text-align-center has-neutral-700-color has-text-color has-lg-font-size"><?php echo esc_html_x( 'Sixty new pieces, and the last of the summer stock at half price.', 'Pattern supporting text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/shop"><?php echo esc_html_x( 'Shop the edit', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:cover -->
<!-- /wp:suitemart/slide -->

<!-- wp:suitemart/slide -->
<!-- wp:cover {"overlayColor":"contrast","isUserOverlayColor":true,"minHeight":480,"contentPosition":"center center","layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-cover" style="min-height:480px"><span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":2,"textColor":"base","fontSize":"3xl"} -->
<h2 class="wp-block-heading has-text-align-center has-base-color has-text-color has-3-xl-font-size"><?php echo esc_html_x( 'Free delivery, always', 'Pattern headline', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"neutral-300","fontSize":"lg"} -->
<p class="has-text-align-center has-neutral-300-color has-text-color has-lg-font-size"><?php echo esc_html_x( 'On every order over £50, to anywhere in the country.', 'Pattern supporting text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"base","textColor":"contrast"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-contrast-color has-base-background-color has-text-color has-background wp-element-button" href="#"><?php echo esc_html_x( 'See the details', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:cover -->
<!-- /wp:suitemart/slide -->

<!-- wp:suitemart/slide -->
<!-- wp:cover {"overlayColor":"neutral-200","isUserOverlayColor":true,"minHeight":480,"contentPosition":"center center","layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-cover" style="min-height:480px"><span aria-hidden="true" class="wp-block-cover__background has-neutral-200-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":2,"textColor":"contrast","fontSize":"3xl"} -->
<h2 class="wp-block-heading has-text-align-center has-contrast-color has-text-color has-3-xl-font-size"><?php echo esc_html_x( 'Made to be repaired', 'Pattern headline', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"neutral-700","fontSize":"lg"} -->
<p class="has-text-align-center has-neutral-700-color has-text-color has-lg-font-size"><?php echo esc_html_x( 'Send anything back for a mend, for as long as you own it.', 'Pattern supporting text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html_x( 'How it works', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:cover -->
<!-- /wp:suitemart/slide -->
<!-- /wp:suitemart/slider -->
