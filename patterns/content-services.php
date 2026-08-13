<?php
/**
 * Title: Services, four cards
 * Slug: suitemart/content-services
 * Categories: suitemart/content, services, featured
 * Description: Four bordered cards, each an icon above a short description of what you do.
 * Keywords: services, offering, cards, icons, what we do
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_services = array(
	array(
		'icon'  => 'package',
		'title' => _x( 'Made to order', 'Pattern heading', 'suitemart' ),
		'text'  => _x( 'Tell us the measurements and we will cut to them. Four weeks, start to finish.', 'Pattern text', 'suitemart' ),
	),
	array(
		'icon'  => 'refresh-cw',
		'title' => _x( 'Repairs', 'Pattern heading', 'suitemart' ),
		'text'  => _x( 'Anything we have sold, we will mend. Send it back and we will quote before starting.', 'Pattern text', 'suitemart' ),
	),
	array(
		'icon'  => 'users',
		'title' => _x( 'In-store advice', 'Pattern heading', 'suitemart' ),
		'text'  => _x( 'Book half an hour with someone who knows the range and leave with a plan.', 'Pattern text', 'suitemart' ),
	),
	array(
		'icon'  => 'truck',
		'title' => _x( 'Delivery and setup', 'Pattern heading', 'suitemart' ),
		'text'  => _x( 'We carry it in, put it where you want it, and take the packaging away.', 'Pattern text', 'suitemart' ),
	),
);
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":2,"align":"wide"} -->
<h2 class="wp-block-heading alignwide"><?php echo esc_html_x( 'What we do', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40","margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"grid","columnCount":4,"minimumColumnWidth":"14rem"}} -->
<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--50)">
<?php foreach ( $sm_services as $sm_service ) : ?>
<!-- wp:suitemart/infobox {"icon":"<?php echo esc_attr( $sm_service['icon'] ); ?>","iconSize":32,"title":"<?php echo esc_attr( $sm_service['title'] ); ?>","description":"<?php echo esc_attr( $sm_service['text'] ); ?>","orientation":"vertical","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}},"border":{"width":"1px","radius":"var:custom|radius|md"}},"borderColor":"neutral-200"} /-->
<?php endforeach; ?>
</div>
<!-- /wp:group --></div>
<!-- /wp:group -->
