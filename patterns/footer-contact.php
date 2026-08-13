<?php
/**
 * Title: Footer, with opening hours
 * Slug: suitemart/footer-contact
 * Categories: suitemart/footer
 * Block Types: core/template-part/footer
 * Description: Address, opening hours and a map, for shops with a door people can walk through.
 * Keywords: footer, contact, address, hours, map, local
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"sm-footer sm-footer--contact","backgroundColor":"neutral-100","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|50"}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group sm-footer sm-footer--contact has-neutral-100-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"30%"} -->
<div class="wp-block-column" style="flex-basis:30%"><!-- wp:heading {"level":2,"fontSize":"sm","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.08em"}}} -->
<h2 class="wp-block-heading has-sm-font-size" style="text-transform:uppercase;letter-spacing:0.08em"><?php echo esc_html_x( 'Find us', 'Pattern footer heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><?php echo esc_html_x( '14 Bridge Street', 'Pattern address line', 'suitemart' ); ?><br><?php echo esc_html_x( 'Manchester M3 1JR', 'Pattern address line', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><a href="tel:+441610000000"><?php echo esc_html_x( '0161 000 0000', 'Pattern phone number', 'suitemart' ); ?></a><br><a href="mailto:hello@example.com">hello@example.com</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"30%"} -->
<div class="wp-block-column" style="flex-basis:30%"><!-- wp:heading {"level":2,"fontSize":"sm","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.08em"}}} -->
<h2 class="wp-block-heading has-sm-font-size" style="text-transform:uppercase;letter-spacing:0.08em"><?php echo esc_html_x( 'Opening hours', 'Pattern footer heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<?php
/*
 * A definition list rather than a table: these are pairs, not a grid, and a
 * screen reader reads a two-column table of them one cell at a time.
 */
?>
<!-- wp:list {"className":"is-style-none","fontSize":"sm"} -->
<ul class="wp-block-list is-style-none has-sm-font-size"><!-- wp:list-item -->
<li><?php echo esc_html_x( 'Monday to Friday · 9am – 6pm', 'Pattern opening hours', 'suitemart' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( 'Saturday · 10am – 5pm', 'Pattern opening hours', 'suitemart' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( 'Sunday · closed', 'Pattern opening hours', 'suitemart' ); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/map {"height":240,"heightMobile":200,"zoom":15,"title":"<?php echo esc_attr_x( 'Where the shop is', 'Pattern label', 'suitemart' ); ?>","requireConsent":true,"style":{"border":{"radius":"var:custom|radius|md"}}} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:separator {"align":"wide","backgroundColor":"neutral-300","className":"is-style-wide"} -->
<hr class="wp-block-separator alignwide has-text-color has-neutral-300-color has-alpha-channel-opacity has-neutral-300-background-color has-background is-style-wide"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"align":"center","textColor":"neutral-600","fontSize":"sm"} -->
<p class="has-text-align-center has-neutral-600-color has-text-color has-sm-font-size"><?php echo esc_html_x( '© Site Title', 'Pattern copyright text', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
