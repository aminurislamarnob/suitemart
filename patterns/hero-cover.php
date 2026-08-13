<?php
/**
 * Title: Hero, full-bleed image
 * Slug: suitemart/hero-cover
 * Categories: suitemart/hero, banner
 * Description: One edge-to-edge image with the headline sitting low over it.
 * Keywords: hero, cover, full width, image, banner
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<?php
/*
 * `contrast` stands in for the photograph, and the text is set on `base` — so
 * the pattern reads correctly before an image is chosen, and still reads
 * correctly once one is, because the dim layer keeps the ground dark.
 */
?>
<!-- wp:cover {"overlayColor":"contrast","isUserOverlayColor":true,"dimRatio":70,"minHeight":620,"contentPosition":"bottom left","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-cover alignfull has-custom-content-position is-position-bottom-left" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70);min-height:620px"><span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-70 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained","contentSize":"620px","justifyContent":"left"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"textColor":"base","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.1em","fontWeight":"600"}},"fontSize":"sm"} -->
<p class="has-base-color has-text-color has-sm-font-size" style="font-weight:600;text-transform:uppercase;letter-spacing:0.1em"><?php echo esc_html_x( 'Winter 26', 'Pattern eyebrow text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"textColor":"base","fontSize":"3xl"} -->
<h1 class="wp-block-heading has-base-color has-text-color has-3-xl-font-size"><?php echo esc_html_x( 'Built for the weather you actually get', 'Pattern headline', 'suitemart' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"neutral-300","fontSize":"lg"} -->
<p class="has-neutral-300-color has-text-color has-lg-font-size"><?php echo esc_html_x( 'Outerwear tested through three winters before it goes on sale.', 'Pattern supporting text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--20)"><!-- wp:button {"backgroundColor":"base","textColor":"contrast"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-contrast-color has-base-background-color has-text-color has-background wp-element-button" href="/shop"><?php echo esc_html_x( 'Shop outerwear', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->
