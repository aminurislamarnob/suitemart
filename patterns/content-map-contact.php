<?php
/**
 * Title: Map and contact details
 * Slug: suitemart/content-map-contact
 * Categories: suitemart/content, contact
 * Description: A map beside an address, opening hours and contact links.
 * Keywords: contact, map, address, location, hours
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"verticalAlignment":"top","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns are-vertically-aligned-top"><!-- wp:column {"verticalAlignment":"top","width":"40%"} -->
<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:40%"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Visit the shop', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:suitemart/infobox {"icon":"map-pin","iconSize":24,"title":"<?php echo esc_attr_x( 'Address', 'Pattern heading', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Replace with your street address, town and postcode.', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /-->

<!-- wp:suitemart/infobox {"icon":"clock","iconSize":24,"title":"<?php echo esc_attr_x( 'Opening hours', 'Pattern heading', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Monday to Saturday, 9am until 6pm. Closed on Sundays.', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /-->

<!-- wp:suitemart/infobox {"icon":"phone","iconSize":24,"title":"<?php echo esc_attr_x( 'Phone', 'Pattern heading', 'suitemart' ); ?>","description":"<?php echo esc_attr_x( 'Replace with the number customers should call.', 'Pattern text', 'suitemart' ); ?>","orientation":"horizontal"} /--></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"top"} -->
<?php // No address is set: the block shows an editor placeholder until one is, rather than a map of somewhere arbitrary. ?>
<div class="wp-block-column is-vertically-aligned-top"><!-- wp:suitemart/map {"source":"address","address":"","height":420,"heightMobile":260,"title":"<?php echo esc_attr_x( 'Map showing the shop location', 'Pattern accessible title', 'suitemart' ); ?>","requireConsent":true,"style":{"border":{"radius":"10px"}}} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
