<?php
/**
 * Title: Home — shop
 * Slug: suitemart/page-home-shop
 * Categories: suitemart/page
 * Block Types: core/post-content
 * Post Types: page
 * Description: A complete shop home page: hero, category grid, featured products and a newsletter call to action.
 * Keywords: home, front page, shop, landing
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:pattern {"slug":"suitemart/hero-shop"} /-->

<?php if ( suitemart_has_woocommerce() ) : ?>
<!-- wp:pattern {"slug":"suitemart/commerce-featured-products"} /-->
<?php endif; ?>

<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"},"blockGap":"var:preset|spacing|50"}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"textAlign":"center","level":2,"fontSize":"2xl"} -->
<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size"><?php echo esc_html_x( 'Why shop with us', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"fontSize":"lg"} -->
<h3 class="wp-block-heading has-lg-font-size"><?php echo esc_html_x( 'Free delivery', 'Pattern feature heading', 'suitemart' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"neutral-600"} -->
<p class="has-neutral-600-color has-text-color"><?php echo esc_html_x( 'On every order over the threshold you set, to anywhere you ship.', 'Pattern feature text', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"fontSize":"lg"} -->
<h3 class="wp-block-heading has-lg-font-size"><?php echo esc_html_x( 'Easy returns', 'Pattern feature heading', 'suitemart' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"neutral-600"} -->
<p class="has-neutral-600-color has-text-color"><?php echo esc_html_x( 'Thirty days to change your mind, no questions asked.', 'Pattern feature text', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"fontSize":"lg"} -->
<h3 class="wp-block-heading has-lg-font-size"><?php echo esc_html_x( 'Secure checkout', 'Pattern feature heading', 'suitemart' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"neutral-600"} -->
<p class="has-neutral-600-color has-text-color"><?php echo esc_html_x( 'Every payment gateway WooCommerce supports, working out of the box.', 'Pattern feature text', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","backgroundColor":"neutral-900","textColor":"neutral-200","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"},"blockGap":"var:preset|spacing|40"},"elements":{"link":{"color":{"text":"var:preset|color|neutral-200"},":hover":{"color":{"text":"var:preset|color|base"}}}}},"layout":{"type":"constrained","contentSize":"560px"}} -->
<div class="wp-block-group alignfull has-neutral-200-color has-neutral-900-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"textAlign":"center","level":2,"textColor":"base","fontSize":"xl"} -->
<h2 class="wp-block-heading has-text-align-center has-base-color has-text-color has-xl-font-size"><?php echo esc_html_x( 'Get the good stuff first', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php echo esc_html_x( 'New arrivals and members-only offers, straight to your inbox.', 'Pattern supporting text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html_x( 'Subscribe', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
