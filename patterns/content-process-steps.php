<?php
/**
 * Title: How it works, four steps
 * Slug: suitemart/content-process-steps
 * Categories: suitemart/content, services
 * Description: A numbered sequence explaining what happens between order and delivery.
 * Keywords: steps, process, how it works, onboarding, numbered
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * Numbered because the order carries meaning — step three cannot happen before
 * step two. Do not copy this numbering onto a set of unordered features; there
 * it is decoration pretending to be information.
 */
$sm_steps = array(
	array(
		'title' => _x( 'Choose and order', 'Pattern heading', 'suitemart' ),
		'text'  => _x( 'Pick a size, add it to the basket, and check out as a guest if you would rather not have an account.', 'Pattern text', 'suitemart' ),
	),
	array(
		'title' => _x( 'We pack it', 'Pattern heading', 'suitemart' ),
		'text'  => _x( 'Orders in before 3pm leave the same working day, in packaging that goes in the recycling.', 'Pattern text', 'suitemart' ),
	),
	array(
		'title' => _x( 'Track the parcel', 'Pattern heading', 'suitemart' ),
		'text'  => _x( 'A tracking link arrives by email the moment the courier scans it. No account needed to use it.', 'Pattern text', 'suitemart' ),
	),
	array(
		'title' => _x( 'Keep it or send it back', 'Pattern heading', 'suitemart' ),
		'text'  => _x( 'Thirty days to decide. Returns are free and the refund goes back the way you paid.', 'Pattern text', 'suitemart' ),
	),
);
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'How ordering works', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|50","margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"grid","columnCount":4,"minimumColumnWidth":"14rem"}} -->
<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--50)">
<?php foreach ( $sm_steps as $sm_index => $sm_step ) : ?>
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20","padding":{"top":"var:preset|spacing|40"}},"border":{"top":{"color":"var:preset|color|primary","width":"2px","style":"solid"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--primary);border-top-style:solid;border-top-width:2px;padding-top:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"textColor":"primary","style":{"typography":{"fontWeight":"700"}},"fontSize":"sm"} -->
<p class="has-primary-color has-text-color has-sm-font-size" style="font-weight:700"><?php echo esc_html( number_format_i18n( $sm_index + 1 ) ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"fontSize":"md"} -->
<h3 class="wp-block-heading has-md-font-size"><?php echo esc_html( $sm_step['title'] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"neutral-700","fontSize":"sm"} -->
<p class="has-neutral-700-color has-text-color has-sm-font-size"><?php echo esc_html( $sm_step['text'] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
<?php endforeach; ?>
</div>
<!-- /wp:group --></div>
<!-- /wp:group -->
