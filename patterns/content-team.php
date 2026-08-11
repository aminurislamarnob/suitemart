<?php
/**
 * Title: Team grid
 * Slug: suitemart/content-team
 * Categories: suitemart/content, about
 * Description: Four team members with portraits, roles and social links.
 * Keywords: team, staff, about, people, profiles
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_members = array(
	array( _x( 'Head of buying', 'Pattern role', 'suitemart' ) ),
	array( _x( 'Studio lead', 'Pattern role', 'suitemart' ) ),
	array( _x( 'Customer care', 'Pattern role', 'suitemart' ) ),
	array( _x( 'Logistics', 'Pattern role', 'suitemart' ) ),
);
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'The people behind the shop', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"},"margin":{"top":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns" style="margin-top:var(--wp--preset--spacing--50)">
<?php foreach ( $sm_members as $sm_member ) : ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/team-member {"name":"<?php echo esc_attr_x( 'Full name', 'Pattern name', 'suitemart' ); ?>","role":"<?php echo esc_attr( $sm_member[0] ); ?>","bio":"<?php echo esc_attr_x( 'One or two lines on what they do and why it matters to a customer.', 'Pattern text', 'suitemart' ); ?>"} -->
<!-- wp:social-links {"size":"has-small-icon-size"} -->
<ul class="wp-block-social-links"></ul>
<!-- /wp:social-links -->
<!-- /wp:suitemart/team-member --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
