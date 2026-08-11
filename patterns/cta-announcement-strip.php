<?php
/**
 * Title: Announcement strip
 * Slug: suitemart/cta-announcement-strip
 * Categories: suitemart/cta, suitemart/header, banner
 * Description: A slow-scrolling strip of short promises, with a pause control.
 * Keywords: ticker, marquee, announcement, promo, usp
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:suitemart/marquee {"align":"full","speed":40,"ariaLabel":"<?php echo esc_attr_x( 'Store announcements', 'Pattern label', 'suitemart' ); ?>","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}},"typography":{"textTransform":"uppercase","letterSpacing":"0.08em"}},"backgroundColor":"contrast","textColor":"base","fontSize":"sm"} -->
<!-- wp:suitemart/marquee-item -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Free delivery over £50', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:suitemart/marquee-item -->

<!-- wp:suitemart/marquee-item -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Thirty-day returns, no questions', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:suitemart/marquee-item -->

<!-- wp:suitemart/marquee-item -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Order before 3pm for same-day dispatch', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:suitemart/marquee-item -->

<!-- wp:suitemart/marquee-item -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Every payment method WooCommerce supports', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:suitemart/marquee-item -->
<!-- /wp:suitemart/marquee -->
