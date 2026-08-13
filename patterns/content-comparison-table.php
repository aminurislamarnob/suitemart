<?php
/**
 * Title: Comparison table
 * Slug: suitemart/content-comparison-table
 * Categories: suitemart/content, text, services
 * Description: A plan-by-plan table with a header row and a caption explaining it.
 * Keywords: comparison, table, plans, tiers, features, specifications
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * Words in the cells rather than tick and cross characters. A tick is read out
 * as "check mark" or, in some screen readers, not at all — so a row of them
 * tells a listener nothing about which column they belong to.
 */
$sm_rows = array(
	array(
		_x( 'Delivery', 'Pattern table row heading', 'suitemart' ),
		_x( 'Three to five days', 'Pattern table cell', 'suitemart' ),
		_x( 'Next working day', 'Pattern table cell', 'suitemart' ),
		_x( 'Next day, booked slot', 'Pattern table cell', 'suitemart' ),
	),
	array(
		_x( 'Returns window', 'Pattern table row heading', 'suitemart' ),
		_x( 'Thirty days', 'Pattern table cell', 'suitemart' ),
		_x( 'Sixty days', 'Pattern table cell', 'suitemart' ),
		_x( 'One year', 'Pattern table cell', 'suitemart' ),
	),
	array(
		_x( 'Repairs', 'Pattern table row heading', 'suitemart' ),
		_x( 'Quoted per job', 'Pattern table cell', 'suitemart' ),
		_x( 'Two free a year', 'Pattern table cell', 'suitemart' ),
		_x( 'Unlimited', 'Pattern table cell', 'suitemart' ),
	),
	array(
		_x( 'Support', 'Pattern table row heading', 'suitemart' ),
		_x( 'Email', 'Pattern table cell', 'suitemart' ),
		_x( 'Email and phone', 'Pattern table cell', 'suitemart' ),
		_x( 'A named contact', 'Pattern table cell', 'suitemart' ),
	),
	array(
		_x( 'Yearly cost', 'Pattern table row heading', 'suitemart' ),
		_x( 'Free', 'Pattern table cell', 'suitemart' ),
		_x( '£49', 'Pattern table cell', 'suitemart' ),
		_x( '£129', 'Pattern table cell', 'suitemart' ),
	),
);
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Compare membership levels', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:table {"align":"wide","hasFixedLayout":true,"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<figure class="wp-block-table alignwide" style="margin-top:var(--wp--preset--spacing--50)"><table class="has-fixed-layout"><thead><tr><th></th><th scope="col"><?php echo esc_html_x( 'Standard', 'Pattern table heading', 'suitemart' ); ?></th><th scope="col"><?php echo esc_html_x( 'Plus', 'Pattern table heading', 'suitemart' ); ?></th><th scope="col"><?php echo esc_html_x( 'Trade', 'Pattern table heading', 'suitemart' ); ?></th></tr></thead><tbody>
<?php foreach ( $sm_rows as $sm_row ) : ?>
<tr><th scope="row"><?php echo esc_html( $sm_row[0] ); ?></th><td><?php echo esc_html( $sm_row[1] ); ?></td><td><?php echo esc_html( $sm_row[2] ); ?></td><td><?php echo esc_html( $sm_row[3] ); ?></td></tr>
<?php endforeach; ?>
</tbody></table><figcaption class="wp-element-caption"><?php echo esc_html_x( 'Prices exclude VAT. Trade membership needs a business account.', 'Pattern table caption', 'suitemart' ); ?></figcaption></figure>
<!-- /wp:table --></div>
<!-- /wp:group -->
