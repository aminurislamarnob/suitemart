<?php
/**
 * Title: Testimonials grid
 * Slug: suitemart/content-testimonials
 * Categories: suitemart/content, testimonials
 * Description: Three customer quotes with star ratings.
 * Keywords: reviews, quotes, social proof, ratings, customers
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_quotes = array(
	_x( 'Arrived a day early and packaged properly. The size guide was accurate, which almost never happens.', 'Pattern quote', 'suitemart' ),
	_x( 'I had to return one item and it took about two minutes. Refund landed before the week was out.', 'Pattern quote', 'suitemart' ),
	_x( 'Third order this year. The quality has been consistent, which is the whole reason I keep coming back.', 'Pattern quote', 'suitemart' ),
);
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'What customers say', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:suitemart/testimonials {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<?php foreach ( $sm_quotes as $sm_quote ) : ?>
<!-- wp:suitemart/testimonial {"quote":"<?php echo esc_attr( $sm_quote ); ?>","author":"<?php echo esc_attr_x( 'Verified buyer', 'Pattern attribution', 'suitemart' ); ?>","role":"<?php echo esc_attr_x( 'Ordered last month', 'Pattern attribution', 'suitemart' ); ?>","rating":5,"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}},"border":{"radius":"8px","width":"1px"}},"borderColor":"neutral-200"} /-->
<?php endforeach; ?>
<!-- /wp:suitemart/testimonials --></div>
<!-- /wp:group -->
