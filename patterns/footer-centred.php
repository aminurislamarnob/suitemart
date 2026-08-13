<?php
/**
 * Title: Footer, centred
 * Slug: suitemart/footer-centred
 * Categories: suitemart/footer
 * Block Types: core/template-part/footer
 * Description: Logo, one row of links and the copyright, all centred on a light ground.
 * Keywords: footer, centred, simple, light, links
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"sm-footer sm-footer--centred","backgroundColor":"neutral-100","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|40"},"border":{"top":{"color":"var:preset|color|neutral-200","style":"solid","width":"1px"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group sm-footer sm-footer--centred has-border-color has-neutral-100-background-color has-background" style="border-top-color:var(--wp--preset--color--neutral-200);border-top-style:solid;border-top-width:1px;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center","verticalAlignment":"center"}} -->
<div class="wp-block-group"><!-- wp:site-logo {"width":40} /-->

<!-- wp:site-title {"level":0,"textAlign":"center","fontSize":"lg"} /--></div>
<!-- /wp:group -->

<!-- wp:paragraph {"align":"center","textColor":"neutral-700","fontSize":"sm"} -->
<p class="has-text-align-center has-neutral-700-color has-text-color has-sm-font-size"><?php echo esc_html_x( 'Independent since 2014, shipping worldwide from one small workshop.', 'Pattern footer text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"center"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><a href="/shop"><?php echo esc_html_x( 'Shop', 'Pattern footer link', 'suitemart' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><a href="/about"><?php echo esc_html_x( 'About', 'Pattern footer link', 'suitemart' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><a href="#"><?php echo esc_html_x( 'Delivery', 'Pattern footer link', 'suitemart' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><a href="#"><?php echo esc_html_x( 'Returns', 'Pattern footer link', 'suitemart' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><a href="/contact"><?php echo esc_html_x( 'Contact', 'Pattern footer link', 'suitemart' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:social-links {"iconColor":"neutral-700","iconColorValue":"var(--wp--preset--color--neutral-700)","className":"is-style-logos-only","layout":{"type":"flex","justifyContent":"center"}} -->
<ul class="wp-block-social-links has-icon-color is-style-logos-only"><!-- wp:social-link {"url":"#","service":"instagram"} /-->

<!-- wp:social-link {"url":"#","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"x"} /--></ul>
<!-- /wp:social-links -->

<!-- wp:paragraph {"align":"center","textColor":"neutral-600","fontSize":"sm"} -->
<p class="has-text-align-center has-neutral-600-color has-text-color has-sm-font-size"><?php echo esc_html_x( '© Site Title', 'Pattern copyright text', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
