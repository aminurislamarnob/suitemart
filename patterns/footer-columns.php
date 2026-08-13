<?php
/**
 * Title: Footer, four columns with a newsletter
 * Slug: suitemart/footer-columns
 * Categories: suitemart/footer
 * Block Types: core/template-part/footer
 * Description: Link columns, a newsletter signup and a payment row, on a dark ground.
 * Keywords: footer, columns, newsletter, links, dark
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<?php
/*
 * Link colours are set on the group rather than on each list, because a dark
 * footer is the one place the default link colour — `primary`, chosen against
 * a light page — has nothing behind it to read against.
 */
?>
<!-- wp:group {"className":"sm-footer","backgroundColor":"neutral-900","textColor":"neutral-200","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|50"},"elements":{"link":{"color":{"text":"var:preset|color|neutral-200"},":hover":{"color":{"text":"var:preset|color|base"}}}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group sm-footer has-neutral-200-color has-neutral-900-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"34%"} -->
<div class="wp-block-column" style="flex-basis:34%"><!-- wp:site-title {"level":0,"style":{"typography":{"textDecoration":"none"}},"textColor":"base","fontSize":"lg"} /-->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"fontSize":"sm"} -->
<p class="has-sm-font-size" style="margin-top:var(--wp--preset--spacing--30)"><?php echo esc_html_x( 'One letter a month: what is new, what is back in stock, and nothing else.', 'Pattern footer text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--30)"><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"base","textColor":"contrast","fontSize":"sm"} -->
<div class="wp-block-button has-custom-font-size has-sm-font-size"><a class="wp-block-button__link has-contrast-color has-base-background-color has-text-color has-background wp-element-button" href="#"><?php echo esc_html_x( 'Join the list', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":2,"textColor":"base","fontSize":"sm"} -->
<h2 class="wp-block-heading has-base-color has-text-color has-sm-font-size"><?php echo esc_html_x( 'Shop', 'Pattern footer heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-none","fontSize":"sm"} -->
<ul class="wp-block-list is-style-none has-sm-font-size"><!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'New arrivals', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Best sellers', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Gift cards', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Sale', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":2,"textColor":"base","fontSize":"sm"} -->
<h2 class="wp-block-heading has-base-color has-text-color has-sm-font-size"><?php echo esc_html_x( 'Help', 'Pattern footer heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-none","fontSize":"sm"} -->
<ul class="wp-block-list is-style-none has-sm-font-size"><!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Delivery', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Returns', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Size guide', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="/contact"><?php echo esc_html_x( 'Contact us', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":2,"textColor":"base","fontSize":"sm"} -->
<h2 class="wp-block-heading has-base-color has-text-color has-sm-font-size"><?php echo esc_html_x( 'Company', 'Pattern footer heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-none","fontSize":"sm"} -->
<ul class="wp-block-list is-style-none has-sm-font-size"><!-- wp:list-item -->
<li><a href="/about"><?php echo esc_html_x( 'About us', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Sustainability', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Careers', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Privacy', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:separator {"align":"wide","backgroundColor":"neutral-700","className":"is-style-wide"} -->
<hr class="wp-block-separator alignwide has-text-color has-neutral-700-color has-alpha-channel-opacity has-neutral-700-background-color has-background is-style-wide"/>
<!-- /wp:separator -->

<!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><?php echo esc_html_x( '© Site Title. All rights reserved.', 'Pattern copyright text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:social-links {"iconColor":"base","iconColorValue":"var(--wp--preset--color--base)","className":"is-style-logos-only"} -->
<ul class="wp-block-social-links has-icon-color is-style-logos-only"><!-- wp:social-link {"url":"#","service":"instagram"} /-->

<!-- wp:social-link {"url":"#","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"pinterest"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
