<?php
/**
 * Title: Floating offer panel
 * Slug: suitemart/cta-floating-offer
 * Categories: suitemart/cta, banner
 * Description: A small panel pinned to the corner of the screen after the visitor has scrolled, offering a discount code. Closeable, and it stays closed.
 * Keywords: floating, sticky, promo, discount, corner
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * The scroll trigger rather than an immediate one: a panel that covers part of
 * the page before the visitor has read any of it is the thing everyone closes
 * without looking. "rememberKey" is fixed here because a pattern is inserted
 * rather than built, so there is no client id to take one from — change it if
 * you use this pattern twice on one site and want the two panels forgotten
 * separately.
 */

?>
<!-- wp:suitemart/floating-block {"position":"bottom-start","trigger":"scroll","threshold":800,"maxWidth":320,"remember":true,"rememberKey":"floatingoffer","hideOnMobile":true,"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}}} -->
<!-- wp:heading {"level":3,"fontSize":"lg"} -->
<h3 class="wp-block-heading has-lg-font-size"><?php echo esc_html_x( '10% off your first order', 'Pattern text', 'suitemart' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><?php echo esc_html_x( 'Use the code WELCOME10 at checkout. One per customer.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"fontSize":"sm"} -->
<div class="wp-block-button has-custom-font-size has-sm-font-size"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html_x( 'Start shopping', 'Pattern text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
<!-- /wp:suitemart/floating-block -->
