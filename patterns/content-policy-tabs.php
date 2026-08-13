<?php
/**
 * Title: Delivery and returns tabs
 * Slug: suitemart/content-policy-tabs
 * Categories: suitemart/content, text, services
 * Description: Delivery, returns and contact details behind three tabs, for a shop policy page.
 * Keywords: delivery, returns, policy, shipping, tabs, help
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * Not `alignwide`. A tab panel is a constrained layout, so its paragraphs stay
 * at `contentSize` and centre themselves inside whatever width the panel has —
 * in a 1280px group that leaves the tab strip hard left and the prose floating
 * in the middle of it. At the content width the two line up.
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":2,"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"}}}} -->
<h2 class="wp-block-heading" style="margin-bottom:var(--wp--preset--spacing--50)"><?php echo esc_html_x( 'Delivery, returns and getting hold of us', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<?php
/*
 * `activation: manual` — arrow keys move between the tabs and Enter or Space
 * opens one, rather than the panel changing as focus moves. With this much text
 * behind each tab, automatic activation means a keyboard user swapping the
 * whole panel out three times on the way to the one they wanted.
 */
?>
<!-- wp:suitemart/tabs {"orientation":"horizontal","activation":"manual"} -->
<!-- wp:suitemart/tab {"label":"<?php echo esc_attr_x( 'Delivery', 'Pattern tab label', 'suitemart' ); ?>","icon":"truck"} -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Orders placed before 3pm on a working day are dispatched the same day. Standard delivery takes three to five days and is free above your free-shipping threshold; next-day is available at checkout until stock runs out for that day.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'We ship to twelve countries. Duties are calculated at checkout, so nothing further is charged when the parcel arrives.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:suitemart/tab -->

<!-- wp:suitemart/tab {"label":"<?php echo esc_attr_x( 'Returns', 'Pattern tab label', 'suitemart' ); ?>","icon":"refresh-cw"} -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Anything unworn can go back within thirty days. Start the return from your account page and we will email a label; the refund goes back the way you paid, usually within five working days of the parcel reaching us.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Faulty items are covered for two years on top of your statutory rights. Send a photograph first and we will often settle it without the item coming back at all.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:suitemart/tab -->

<!-- wp:suitemart/tab {"label":"<?php echo esc_attr_x( 'Contact', 'Pattern tab label', 'suitemart' ); ?>","icon":"mail"} -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'The quickest route is email — we answer within one working day, and the reply comes from whoever picked your order rather than from a queue.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group"><!-- wp:suitemart/infobox {"icon":"mail","iconSize":20,"description":"<?php echo esc_attr_x( 'hello@example.com', 'Pattern email address', 'suitemart' ); ?>","orientation":"horizontal"} /-->

<!-- wp:suitemart/infobox {"icon":"phone","iconSize":20,"description":"<?php echo esc_attr_x( '01234 567 890, weekdays 9am – 5pm', 'Pattern phone number', 'suitemart' ); ?>","orientation":"horizontal"} /--></div>
<!-- /wp:group -->
<!-- /wp:suitemart/tab -->
<!-- /wp:suitemart/tabs --></div>
<!-- /wp:group -->
