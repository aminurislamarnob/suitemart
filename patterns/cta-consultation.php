<?php
/**
 * Title: Book a consultation
 * Slug: suitemart/cta-consultation
 * Categories: suitemart/cta, services, call-to-action
 * Description: A centred prompt to book time with someone, with what to expect from it.
 * Keywords: booking, consultation, appointment, advice, call, service
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|40"},"border":{"width":"1px","radius":"var:custom|radius|lg"}},"borderColor":"neutral-200","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide has-border-color has-neutral-200-border-color" style="border-width:1px;border-radius:var(--wp--custom--radius--lg);padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--50)"><!-- wp:heading {"textAlign":"center","level":2,"fontSize":"2xl"} -->
<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size"><?php echo esc_html_x( 'Half an hour with someone who knows the range', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"neutral-700","fontSize":"lg"} -->
<p class="has-text-align-center has-neutral-700-color has-text-color has-lg-font-size"><?php echo esc_html_x( 'Bring measurements, a photograph, or just an idea. Free, no obligation, and you leave with a written quote.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|50"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"center"}} -->
<div class="wp-block-group"><!-- wp:suitemart/infobox {"icon":"clock","iconSize":20,"description":"<?php echo esc_attr_x( 'Thirty minutes', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /-->

<!-- wp:suitemart/infobox {"icon":"users","iconSize":20,"description":"<?php echo esc_attr_x( 'In the shop or over video', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /-->

<!-- wp:suitemart/infobox {"icon":"calendar","iconSize":20,"description":"<?php echo esc_attr_x( 'Usually free within the week', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /--></div>
<!-- /wp:group -->

<?php
/*
 * A link to whichever booking tool the merchant already runs, rather than a
 * date picker: a theme that offered slots would have to know the calendar
 * behind them, and one that guessed would take bookings nobody can honour.
 */
?>
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html_x( 'Find a time', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
