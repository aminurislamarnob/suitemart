<?php
/**
 * Title: Event countdown
 * Slug: suitemart/content-event-countdown
 * Categories: suitemart/content, suitemart/cta, featured
 * Description: A dated event with the time left to it counting down, and a way to sign up.
 * Keywords: event, countdown, launch, workshop, timer, date
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * The date is computed when the pattern is inserted, not written into the file:
 * a fixed date would already be in the past by the time anyone used this, and
 * the countdown would insert showing its expired text. From insertion on it is
 * an ordinary stored value the merchant edits to the real date.
 */
$sm_event_date = wp_date( 'Y-m-d\TH:i', strtotime( '+14 days 19:00' ) );
?>
<!-- wp:group {"align":"wide","backgroundColor":"contrast","textColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|40"},"border":{"radius":"var:custom|radius|lg"},"elements":{"link":{"color":{"text":"var:preset|color|base"}}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide has-base-color has-contrast-background-color has-text-color has-background has-link-color" style="border-radius:var(--wp--custom--radius--lg);padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--50)"><!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.08em","fontWeight":"600"}},"fontSize":"sm"} -->
<p class="has-text-align-center has-sm-font-size" style="font-weight:600;text-transform:uppercase;letter-spacing:0.08em"><?php echo esc_html_x( 'Saturday evening, doors at seven', 'Pattern eyebrow text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":2,"textColor":"base","fontSize":"2xl"} -->
<h2 class="wp-block-heading has-text-align-center has-base-color has-text-color has-2-xl-font-size"><?php echo esc_html_x( 'Open workshop and late-night shopping', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"lg"} -->
<p class="has-text-align-center has-lg-font-size"><?php echo esc_html_x( 'Watch a piece being made, then take twenty per cent off anything on the shelves.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:suitemart/countdown {"layout":"boxed","endDate":"<?php echo esc_attr( $sm_event_date ); ?>","expiredText":"<?php echo esc_attr_x( 'This one has been and gone — the next is being planned.', 'Pattern countdown expired text', 'suitemart' ); ?>","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} /-->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"base","textColor":"contrast"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-contrast-color has-base-background-color has-text-color has-background wp-element-button" href="#"><?php echo esc_html_x( 'Reserve a place', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
