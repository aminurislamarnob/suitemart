<?php
/**
 * Title: Newsletter band
 * Slug: suitemart/cta-newsletter-band
 * Categories: suitemart/cta, call-to-action
 * Description: A full-width signup band for the middle of a page, with three reasons to bother.
 * Keywords: newsletter, signup, subscribe, email, list
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"full","backgroundColor":"neutral-100","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull has-neutral-100-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--40)"><!-- wp:heading {"textAlign":"center","level":2,"fontSize":"2xl"} -->
<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size"><?php echo esc_html_x( 'One email a month, worth opening', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"neutral-700","fontSize":"lg"} -->
<p class="has-text-align-center has-neutral-700-color has-text-color has-lg-font-size"><?php echo esc_html_x( 'New stock, restocks of the things that sell out, and nothing else.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<?php
/*
 * A button through to a signup page rather than an email field. Collecting an
 * address needs a mailing-list plugin behind it, and a form that posts nowhere
 * is worse than a link — it looks like it worked.
 */
?>
<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html_x( 'Sign up', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|50","margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"center"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--50)"><!-- wp:suitemart/infobox {"icon":"mail","iconSize":20,"description":"<?php echo esc_attr_x( 'Twelve emails a year, no more', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /-->

<!-- wp:suitemart/infobox {"icon":"shield","iconSize":20,"description":"<?php echo esc_attr_x( 'We never pass your address on', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /-->

<!-- wp:suitemart/infobox {"icon":"x","iconSize":20,"description":"<?php echo esc_attr_x( 'Unsubscribe from any of them', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
