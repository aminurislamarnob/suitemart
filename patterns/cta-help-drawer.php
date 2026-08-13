<?php
/**
 * Title: Help drawer
 * Slug: suitemart/cta-help-drawer
 * Categories: suitemart/cta, contact, services
 * Description: A "need a hand?" button that slides a panel of contact routes in from the side.
 * Keywords: help, support, drawer, off-canvas, contact, panel
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * The trigger and the panel are matched by `panelId`, so a page carrying two
 * off-canvas patterns needs two different ids or the first trigger opens both.
 * `help` is deliberately specific rather than "panel".
 */
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|30"},"border":{"width":"1px","radius":"var:custom|radius|lg"}},"borderColor":"neutral-200","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide has-border-color has-neutral-200-border-color" style="border-width:1px;border-radius:var(--wp--custom--radius--lg);padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":2,"fontSize":"xl"} -->
<h2 class="wp-block-heading has-xl-font-size"><?php echo esc_html_x( 'Not sure which size, or which finish?', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"neutral-700"} -->
<p class="has-neutral-700-color has-text-color"><?php echo esc_html_x( 'Ask us before you order. It saves a return, and we would rather answer first.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:suitemart/off-canvas-trigger {"panelId":"help","icon":"phone","label":"<?php echo esc_attr_x( 'Need a hand?', 'Pattern button text', 'suitemart' ); ?>"} /--></div>
<!-- /wp:group -->

<!-- wp:suitemart/off-canvas {"panelId":"help","side":"end","size":"24rem","title":"<?php echo esc_attr_x( 'Talk to us', 'Pattern panel title', 'suitemart' ); ?>"} -->
<!-- wp:paragraph {"textColor":"neutral-700"} -->
<p class="has-neutral-700-color has-text-color"><?php echo esc_html_x( 'Whichever of these suits you. All three reach the same three people.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<?php
/*
 * The three boxes are wrapped in a group of their own so they get a block gap.
 * The panel stacks its children with no gap, so unwrapped they run together —
 * one box's description sits directly against the next one's heading.
 */
?>
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40","margin":{"top":"var:preset|spacing|40"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--40)"><!-- wp:suitemart/infobox {"icon":"phone","iconSize":24,"title":"<?php echo esc_attr_x( '01234 567 890', 'Pattern phone number', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Weekdays, 9am to 5pm', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /-->

<!-- wp:suitemart/infobox {"icon":"mail","iconSize":24,"title":"<?php echo esc_attr_x( 'hello@example.com', 'Pattern email address', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Answered within a working day', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /-->

<!-- wp:suitemart/infobox {"icon":"list","iconSize":24,"title":"<?php echo esc_attr_x( 'Read the size guides', 'Pattern heading', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Measured flat, in centimetres.', 'Pattern text', 'suitemart' ); ?>","url":"#","orientation":"horizontal"} /--></div>
<!-- /wp:group -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-buttons"><!-- wp:button {"width":100} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link wp-element-button" href="/contact"><?php echo esc_html_x( 'Send a message', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
<!-- /wp:suitemart/off-canvas -->
