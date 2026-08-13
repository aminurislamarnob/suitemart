<?php
/**
 * Title: Checkout reassurance row
 * Slug: suitemart/commerce-checkout-trust
 * Categories: suitemart/commerce, woocommerce
 * Description: Four short promises — delivery, returns, payment, support — for a cart or checkout page.
 * Keywords: trust, reassurance, delivery, returns, secure, support
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<?php
/*
 * No WooCommerce guard: every block here is Suitemart's own, so the pattern
 * registers on any site. It is filed under commerce because that is where it
 * belongs in the inserter, not because it needs a shop to render.
 *
 * The claims are placeholders and each one is a promise the merchant has to be
 * able to keep — an unedited "30-day returns" is a term of sale, not copy.
 */
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|40"},"border":{"top":{"width":"1px"},"bottom":{"width":"1px"}}},"borderColor":"neutral-200","layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide has-border-color has-neutral-200-border-color" style="border-top-width:1px;border-bottom-width:1px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)"><!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/infobox {"icon":"truck","iconSize":28,"title":"<?php echo esc_attr_x( 'Free delivery over £50', 'Pattern infobox title', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Tracked, and usually with you in two working days.', 'Pattern infobox text', 'suitemart' ); ?>","titleLevel":3,"orientation":"vertical"} /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/infobox {"icon":"refresh-cw","iconSize":28,"title":"<?php echo esc_attr_x( 'Thirty-day returns', 'Pattern infobox title', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Send anything back unworn, with the label we include.', 'Pattern infobox text', 'suitemart' ); ?>","titleLevel":3,"orientation":"vertical"} /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/infobox {"icon":"shield","iconSize":28,"title":"<?php echo esc_attr_x( 'Secure payment', 'Pattern infobox title', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Handled by your payment provider. We never see your card.', 'Pattern infobox text', 'suitemart' ); ?>","titleLevel":3,"orientation":"vertical"} /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/infobox {"icon":"phone","iconSize":28,"title":"<?php echo esc_attr_x( 'Someone to ask', 'Pattern infobox title', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Weekdays, nine to five, by phone or email.', 'Pattern infobox text', 'suitemart' ); ?>","titleLevel":3,"orientation":"vertical"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
