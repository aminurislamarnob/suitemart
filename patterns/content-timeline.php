<?php
/**
 * Title: Timeline
 * Slug: suitemart/content-timeline
 * Categories: suitemart/content, about
 * Description: A dated sequence of milestones, rendered as an ordered list.
 * Keywords: history, story, milestones, process, roadmap
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_entries = array(
	array( '2019', _x( 'Opened the first shop', 'Pattern heading', 'suitemart' ), _x( 'One room, forty products and a lot of packing tape.', 'Pattern text', 'suitemart' ), true ),
	array( '2021', _x( 'Moved to a real warehouse', 'Pattern heading', 'suitemart' ), _x( 'Same-day dispatch became possible for the first time.', 'Pattern text', 'suitemart' ), true ),
	array( '2023', _x( 'Started shipping worldwide', 'Pattern heading', 'suitemart' ), _x( 'Twelve countries, with duties calculated at checkout.', 'Pattern text', 'suitemart' ), true ),
	array( '2026', _x( 'Carbon-neutral delivery', 'Pattern heading', 'suitemart' ), _x( 'The next milestone, and the one we are working on now.', 'Pattern text', 'suitemart' ), false ),
);
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'How we got here', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:suitemart/timeline {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<?php foreach ( $sm_entries as $sm_entry ) : ?>
<!-- wp:suitemart/timeline-item {"date":"<?php echo esc_attr( $sm_entry[0] ); ?>","title":"<?php echo esc_attr( $sm_entry[1] ); ?>","isComplete":<?php echo $sm_entry[3] ? 'true' : 'false'; ?>} -->
<!-- wp:paragraph -->
<p><?php echo esc_html( $sm_entry[2] ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:suitemart/timeline-item -->
<?php endforeach; ?>
<!-- /wp:suitemart/timeline --></div>
<!-- /wp:group -->
