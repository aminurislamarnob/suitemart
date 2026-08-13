<?php
/**
 * Title: Footer, with a reassurance row
 * Slug: suitemart/footer-reassurance
 * Categories: suitemart/footer, suitemart/commerce
 * Block Types: core/template-part/footer
 * Description: Delivery, returns and support promises across the top, then links and payment methods.
 * Keywords: footer, reassurance, delivery, returns, trust, payment
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"sm-footer sm-footer--reassurance","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group sm-footer sm-footer--reassurance"><!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}},"border":{"top":{"color":"var:preset|color|neutral-200","style":"solid","width":"1px"},"bottom":{"color":"var:preset|color|neutral-200","style":"solid","width":"1px"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull has-border-color" style="border-top-color:var(--wp--preset--color--neutral-200);border-top-style:solid;border-top-width:1px;border-bottom-color:var(--wp--preset--color--neutral-200);border-bottom-style:solid;border-bottom-width:1px;padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/infobox {"icon":"truck","iconSize":28,"title":"<?php echo esc_attr_x( 'Free delivery over £50', 'Pattern heading', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Two to three working days, tracked.', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/infobox {"icon":"refresh-cw","iconSize":28,"title":"<?php echo esc_attr_x( 'Thirty-day returns', 'Pattern heading', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Unworn and unwashed, with the label on.', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/infobox {"icon":"phone","iconSize":28,"title":"<?php echo esc_attr_x( 'Talk to a person', 'Pattern heading', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Weekdays, nine to six, from the shop floor.', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:site-title {"level":0,"fontSize":"lg"} /-->

<!-- wp:paragraph {"textColor":"neutral-700","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"sm"} -->
<p class="has-neutral-700-color has-text-color has-sm-font-size" style="margin-top:var(--wp--preset--spacing--20)"><?php echo esc_html_x( 'A shop, a workshop and four people, in the same building since 2014.', 'Pattern footer text', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":2,"fontSize":"sm","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.08em"}}} -->
<h2 class="wp-block-heading has-sm-font-size" style="text-transform:uppercase;letter-spacing:0.08em"><?php echo esc_html_x( 'Shop', 'Pattern footer heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-none","fontSize":"sm"} -->
<ul class="wp-block-list is-style-none has-sm-font-size"><!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'New arrivals', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Best sellers', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Sale', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":2,"fontSize":"sm","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.08em"}}} -->
<h2 class="wp-block-heading has-sm-font-size" style="text-transform:uppercase;letter-spacing:0.08em"><?php echo esc_html_x( 'Help', 'Pattern footer heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-none","fontSize":"sm"} -->
<ul class="wp-block-list is-style-none has-sm-font-size"><!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Track an order', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#"><?php echo esc_html_x( 'Returns', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="/contact"><?php echo esc_html_x( 'Contact', 'Pattern footer link', 'suitemart' ); ?></a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":2,"fontSize":"sm","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.08em"}}} -->
<h2 class="wp-block-heading has-sm-font-size" style="text-transform:uppercase;letter-spacing:0.08em"><?php echo esc_html_x( 'We accept', 'Pattern footer heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<?php
/*
 * Written out rather than drawn: card-scheme logos are trademarks with their
 * own brand guidelines, and none of them may be bundled with a theme sold
 * commercially.
 */
?>
<!-- wp:paragraph {"textColor":"neutral-700","fontSize":"sm"} -->
<p class="has-neutral-700-color has-text-color has-sm-font-size"><?php echo esc_html_x( 'Visa · Mastercard · American Express · PayPal · Apple Pay', 'Pattern payment methods', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:paragraph {"align":"center","textColor":"neutral-600","fontSize":"sm"} -->
<p class="has-text-align-center has-neutral-600-color has-text-color has-sm-font-size"><?php echo esc_html_x( '© Site Title. All rights reserved.', 'Pattern copyright text', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
