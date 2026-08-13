<?php
/**
 * Title: Newsletter popup
 * Slug: suitemart/cta-newsletter-popup
 * Categories: suitemart/cta
 * Description: A modal dialog offering a discount for a newsletter signup, opened once per visitor after a delay.
 * Keywords: popup, modal, newsletter, signup, discount
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * Twelve seconds, not two. A modal that lands before the visitor has read
 * anything is the interruption everyone closes on reflex, and it is measured
 * from the load rather than from any sign of interest. "onceKey" is fixed here
 * because a pattern is inserted rather than built, so there is no client id to
 * take one from — change it if you use this pattern twice on one site.
 *
 * The form itself is left to the site: the theme ships no mailing list
 * integration, and a fake signup field would be worse than an honest link.
 */

?>
<!-- wp:suitemart/popup {"trigger":"delay","delay":12,"label":"<?php echo esc_attr_x( 'Newsletter signup', 'Pattern label', 'suitemart' ); ?>","onceKey":"newsletterpopup","maxWidth":460,"style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
<!-- wp:heading {"level":2,"fontSize":"xl"} -->
<h2 class="wp-block-heading has-xl-font-size"><?php echo esc_html_x( 'Ten percent off your first order', 'Pattern text', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Join the list for new arrivals and the occasional sale. One email a month, and nothing else.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html_x( 'Sign up', 'Pattern text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
<!-- /wp:suitemart/popup -->
