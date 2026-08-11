<?php
/**
 * Title: Feature row
 * Slug: suitemart/content-features
 * Categories: suitemart/content, featured
 * Description: Three info boxes in a row — delivery, returns and support.
 * Keywords: features, usp, services, trust, icons
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/infobox {"icon":"truck","iconSize":32,"title":"<?php echo esc_attr_x( 'Free delivery', 'Pattern heading', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'On every order above your free-shipping threshold.', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/infobox {"icon":"refresh-cw","iconSize":32,"title":"<?php echo esc_attr_x( 'Easy returns', 'Pattern heading', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Thirty days to change your mind, no questions asked.', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/infobox {"icon":"shield","iconSize":32,"title":"<?php echo esc_attr_x( 'Secure checkout', 'Pattern heading', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Every payment method WooCommerce supports.', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
