<?php
/**
 * Title: Shoppable lookbook
 * Slug: suitemart/content-lookbook
 * Categories: suitemart/content, gallery
 * Description: A room or outfit photograph with markers pinned to it, each opening a short note and a link.
 * Keywords: lookbook, hotspot, shoppable, pins, interiors
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * The markers are placed but the photograph is not: an editor supplies their
 * own, the same way the brands pattern leaves its logos empty. Shipping a
 * stand-in photograph only invites it to be published by mistake, and the
 * pinned positions below are what actually takes the work out of this pattern.
 */
$sm_pins = array(
	array(
		'x'         => 30,
		'y'         => 34,
		'placement' => 'end',
		'label'     => _x( 'Show details about the pendant light', 'Pattern text', 'suitemart' ),
		'title'     => _x( 'Brass pendant', 'Pattern heading', 'suitemart' ),
		'body'      => _x( 'Hand-spun shade on a woven cord. Two metres, shortenable.', 'Pattern text', 'suitemart' ),
	),
	array(
		'x'         => 62,
		'y'         => 58,
		'placement' => 'top',
		'label'     => _x( 'Show details about the armchair', 'Pattern text', 'suitemart' ),
		'title'     => _x( 'Linen armchair', 'Pattern heading', 'suitemart' ),
		'body'      => _x( 'Solid oak frame, removable covers, four weeks to make.', 'Pattern text', 'suitemart' ),
	),
	array(
		'x'         => 18,
		'y'         => 74,
		'placement' => 'top',
		'label'     => _x( 'Show details about the rug', 'Pattern text', 'suitemart' ),
		'title'     => _x( 'Wool flatweave', 'Pattern heading', 'suitemart' ),
		'body'      => _x( 'Undyed wool in three sizes. Reversible, so it wears evenly.', 'Pattern text', 'suitemart' ),
	),
);
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Shop the room', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><?php echo esc_html_x( 'Choose a marker to see what is in the picture.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:suitemart/hotspots {"markerStyle":"number","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<?php foreach ( $sm_pins as $sm_pin ) : ?>
<!-- wp:suitemart/hotspot {"x":<?php echo (int) $sm_pin['x']; ?>,"y":<?php echo (int) $sm_pin['y']; ?>,"placement":"<?php echo esc_attr( $sm_pin['placement'] ); ?>","label":"<?php echo esc_attr( $sm_pin['label'] ); ?>"} -->
<!-- wp:heading {"level":4,"fontSize":"md"} -->
<h4 class="wp-block-heading has-md-font-size"><?php echo esc_html( $sm_pin['title'] ); ?></h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $sm_pin['body'] ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:suitemart/hotspot -->
<?php endforeach; ?>
<!-- /wp:suitemart/hotspots --></div>
<!-- /wp:group -->
