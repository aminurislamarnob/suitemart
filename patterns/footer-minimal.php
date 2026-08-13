<?php
/**
 * Title: Footer, one line
 * Slug: suitemart/footer-minimal
 * Categories: suitemart/footer
 * Block Types: core/template-part/footer
 * Description: A single row: copyright on one side, a handful of links on the other.
 * Keywords: footer, minimal, simple, one line, links
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"sm-footer sm-footer--minimal","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}},"border":{"top":{"color":"var:preset|color|neutral-200","style":"solid","width":"1px"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group sm-footer sm-footer--minimal has-border-color" style="border-top-color:var(--wp--preset--color--neutral-200);border-top-style:solid;border-top-width:1px;padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"textColor":"neutral-600","fontSize":"sm"} -->
<p class="has-neutral-600-color has-text-color has-sm-font-size"><?php echo esc_html_x( '© Site Title', 'Pattern copyright text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><a href="#"><?php echo esc_html_x( 'Delivery', 'Pattern footer link', 'suitemart' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><a href="#"><?php echo esc_html_x( 'Returns', 'Pattern footer link', 'suitemart' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><a href="#"><?php echo esc_html_x( 'Privacy', 'Pattern footer link', 'suitemart' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><a href="/contact"><?php echo esc_html_x( 'Contact', 'Pattern footer link', 'suitemart' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
