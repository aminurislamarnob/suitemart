<?php
/**
 * Title: Store locations
 * Slug: suitemart/content-store-locations
 * Categories: suitemart/content, contact, services
 * Description: Three shops as cards, each with an address, opening hours and a phone number.
 * Keywords: stores, locations, shops, addresses, opening hours, contact
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * The three lines under each shop name are the info box's `description`, not
 * its `title`: a title renders as a heading, and an address is not a heading —
 * it would put nine more entries in the document outline for no navigational
 * gain.
 */
$sm_stores = array(
	array(
		'name'    => _x( 'City centre', 'Pattern store name', 'suitemart' ),
		'address' => _x( '14 Bridge Street, first floor', 'Pattern address', 'suitemart' ),
		'hours'   => _x( 'Mon to Sat, 9am – 6pm', 'Pattern opening hours', 'suitemart' ),
		'phone'   => _x( '01234 567 890', 'Pattern phone number', 'suitemart' ),
	),
	array(
		'name'    => _x( 'The workshop', 'Pattern store name', 'suitemart' ),
		'address' => _x( 'Unit 3, Mill Yard, off Canal Road', 'Pattern address', 'suitemart' ),
		'hours'   => _x( 'Tue to Fri, 10am – 4pm', 'Pattern opening hours', 'suitemart' ),
		'phone'   => _x( '01234 567 891', 'Pattern phone number', 'suitemart' ),
	),
	array(
		'name'    => _x( 'Harbour road', 'Pattern store name', 'suitemart' ),
		'address' => _x( '92 Harbour Road, beside the ferry', 'Pattern address', 'suitemart' ),
		'hours'   => _x( 'Every day, 10am – 5pm', 'Pattern opening hours', 'suitemart' ),
		'phone'   => _x( '01234 567 892', 'Pattern phone number', 'suitemart' ),
	),
);
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Where to find us', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40","margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"grid","columnCount":3,"minimumColumnWidth":"18rem"}} -->
<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--50)">
<?php foreach ( $sm_stores as $sm_store ) : ?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|30"},"border":{"width":"1px","radius":"var:custom|radius|md"}},"borderColor":"neutral-200","layout":{"type":"default"}} -->
<div class="wp-block-group has-border-color has-neutral-200-border-color" style="border-width:1px;border-radius:var(--wp--custom--radius--md);padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:heading {"level":3,"fontSize":"lg"} -->
<h3 class="wp-block-heading has-lg-font-size"><?php echo esc_html( $sm_store['name'] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:suitemart/infobox {"icon":"map-pin","iconSize":20,"description":"<?php echo esc_attr( $sm_store['address'] ); ?>","orientation":"horizontal"} /-->

<!-- wp:suitemart/infobox {"icon":"clock","iconSize":20,"description":"<?php echo esc_attr( $sm_store['hours'] ); ?>","orientation":"horizontal"} /-->

<!-- wp:suitemart/infobox {"icon":"phone","iconSize":20,"description":"<?php echo esc_attr( $sm_store['phone'] ); ?>","orientation":"horizontal"} /--></div>
<!-- /wp:group -->
<?php endforeach; ?>
</div>
<!-- /wp:group --></div>
<!-- /wp:group -->
