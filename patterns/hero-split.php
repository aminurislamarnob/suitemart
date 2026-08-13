<?php
/**
 * Title: Hero, copy beside an image
 * Slug: suitemart/hero-split
 * Categories: suitemart/hero, banner
 * Description: A two-column hero: headline and buttons on one side, a full-height image on the other.
 * Keywords: hero, split, image, banner, columns
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:columns {"align":"wide","verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"48%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:48%"><!-- wp:paragraph {"textColor":"primary","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.08em","fontWeight":"600"}},"fontSize":"sm"} -->
<p class="has-primary-color has-text-color has-sm-font-size" style="font-weight:600;text-transform:uppercase;letter-spacing:0.08em"><?php echo esc_html_x( 'This season', 'Pattern eyebrow text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"fontSize":"3xl"} -->
<h1 class="wp-block-heading has-3-xl-font-size"><?php echo esc_html_x( 'Pieces made to be kept', 'Pattern headline', 'suitemart' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"neutral-700","fontSize":"lg"} -->
<p class="has-neutral-700-color has-text-color has-lg-font-size"><?php echo esc_html_x( 'Small runs, named makers, and a repair service for everything we sell.', 'Pattern supporting text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--30)"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/shop"><?php echo esc_html_x( 'Shop the collection', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html_x( 'Meet the makers', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<?php
/*
 * A cover with no image is a solid block the merchant swaps for their own
 * photograph in one click. Shipping a stock photo instead would mean licensing
 * it for redistribution, and every buyer inheriting the same picture.
 */
?>
<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:cover {"overlayColor":"neutral-200","isUserOverlayColor":true,"minHeight":460,"style":{"border":{"radius":"var:custom|radius|lg"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-cover" style="border-radius:var(--wp--custom--radius--lg);min-height:460px"><span aria-hidden="true" class="wp-block-cover__background has-neutral-200-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","textColor":"neutral-600","fontSize":"sm"} -->
<p class="has-text-align-center has-neutral-600-color has-text-color has-sm-font-size"><?php echo esc_html_x( 'Replace this with a photograph of the collection.', 'Pattern placeholder text', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:cover --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
