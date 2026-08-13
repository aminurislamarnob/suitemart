<?php
/**
 * Title: Cookie notice
 * Slug: suitemart/cta-cookie-notice
 * Categories: suitemart/cta, banner
 * Description: A bottom bar asking for cookie consent, with Accept and Decline given equal weight. Records the choice and announces it; a consent manager still has to act on it.
 * Keywords: cookie, consent, gdpr, privacy, banner
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * Kept out of every template on purpose. A consent notice that appears without
 * the site owner deciding to add it is worse than none: it tells visitors a
 * choice is being honoured when nothing behind it has been wired up yet.
 */

$sm_privacy_url = get_privacy_policy_url();

?>
<!-- wp:suitemart/cookie-notice {"position":"bottom-start"} -->
<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><?php echo esc_html_x( 'We use cookies to run the shop, remember what is in your basket, and understand how the site is used. You can decline everything that is not needed to place an order.', 'Pattern text', 'suitemart' ); ?>
<?php if ( '' !== $sm_privacy_url ) : ?>
<a href="<?php echo esc_url( $sm_privacy_url ); ?>"><?php echo esc_html_x( 'Read our privacy policy', 'Pattern text', 'suitemart' ); ?></a>
<?php endif; ?>
</p>
<!-- /wp:paragraph -->
<!-- /wp:suitemart/cookie-notice -->
