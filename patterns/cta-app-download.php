<?php
/**
 * Title: Get the app
 * Slug: suitemart/cta-app-download
 * Categories: suitemart/cta, call-to-action
 * Description: A two-column prompt to install the shop's app, with what it is good for listed.
 * Keywords: app, download, mobile, install, ios, android
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"align":"wide","verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%"><!-- wp:heading {"level":2,"fontSize":"2xl"} -->
<h2 class="wp-block-heading has-2-xl-font-size"><?php echo esc_html_x( 'Order from your phone in two taps', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"neutral-700"} -->
<p class="has-neutral-700-color has-text-color"><?php echo esc_html_x( 'Saved baskets, delivery tracking that pushes to your lock screen, and a scanner for the barcode on anything you already own.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<?php
/*
 * Text buttons rather than the App Store and Google Play badges: both are
 * trademarked artwork with their own brand guidelines, so a commercially sold
 * theme cannot ship them. Point these at the store listings and swap in the
 * official badges yourself if you want them.
 */
?>
<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html_x( 'Download for iPhone', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html_x( 'Download for Android', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","right":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|30"},"border":{"radius":"var:custom|radius|lg"}},"backgroundColor":"neutral-100"} -->
<div class="wp-block-column is-vertically-aligned-center has-neutral-100-background-color has-background" style="border-radius:var(--wp--custom--radius--lg);padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:suitemart/infobox {"icon":"shopping-bag","iconSize":24,"title":"<?php echo esc_attr_x( 'Baskets that follow you', 'Pattern heading', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Start on a laptop, finish on the bus.', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /-->

<!-- wp:suitemart/infobox {"icon":"truck","iconSize":24,"title":"<?php echo esc_attr_x( 'Live delivery tracking', 'Pattern heading', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'A notification when the van is close.', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /-->

<!-- wp:suitemart/infobox {"icon":"heart","iconSize":24,"title":"<?php echo esc_attr_x( 'Wishlist alerts', 'Pattern heading', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'We tell you when a saved item is back.', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
