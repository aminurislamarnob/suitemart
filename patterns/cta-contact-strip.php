<?php
/**
 * Title: Contact strip
 * Slug: suitemart/cta-contact-strip
 * Categories: suitemart/cta, contact, call-to-action
 * Description: Phone, email and opening hours in one row, for the foot of a page.
 * Keywords: contact, phone, email, hours, support, help
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<?php
/*
 * Tinted rather than dark. The info box draws its icon in `primary`, and
 * `primary` is the one slug a style variation is free to move anywhere in the
 * ramp — over a near-black band it comes out dim in some of the fifteen. On
 * `neutral-100` it is legible in all of them.
 */
?>
<!-- wp:group {"align":"full","backgroundColor":"neutral-100","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull has-neutral-100-background-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|50"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:suitemart/infobox {"icon":"phone","iconSize":24,"title":"<?php echo esc_attr_x( '01234 567 890', 'Pattern phone number', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Weekdays, 9am to 5pm', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /-->

<!-- wp:suitemart/infobox {"icon":"mail","iconSize":24,"title":"<?php echo esc_attr_x( 'hello@example.com', 'Pattern email address', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Answered within a working day', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /-->

<!-- wp:suitemart/infobox {"icon":"map-pin","iconSize":24,"title":"<?php echo esc_attr_x( '14 Bridge Street', 'Pattern address', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Open six days a week', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /-->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contact"><?php echo esc_html_x( 'Contact us', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
