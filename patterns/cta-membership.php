<?php
/**
 * Title: Join the club
 * Slug: suitemart/cta-membership
 * Categories: suitemart/cta, call-to-action, services
 * Description: A membership pitch: what it costs, what it gets you, and one way in.
 * Keywords: membership, loyalty, club, rewards, subscription, join
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_perks = array(
	array(
		'icon'  => 'truck',
		'title' => _x( 'Free next-day delivery', 'Pattern heading', 'suitemart' ),
		'text'  => _x( 'On everything, however small the order.', 'Pattern text', 'suitemart' ),
	),
	array(
		'icon'  => 'refresh-cw',
		'title' => _x( 'Two free repairs a year', 'Pattern heading', 'suitemart' ),
		'text'  => _x( 'Anything we sold you, whenever we sold it.', 'Pattern text', 'suitemart' ),
	),
	array(
		'icon'  => 'clock',
		'title' => _x( 'Early access to new stock', 'Pattern heading', 'suitemart' ),
		'text'  => _x( 'Forty-eight hours before anyone else.', 'Pattern text', 'suitemart' ),
	),
);
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"align":"wide","verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"42%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:42%"><!-- wp:paragraph {"textColor":"primary","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.08em","fontWeight":"600"}},"fontSize":"sm"} -->
<p class="has-primary-color has-text-color has-sm-font-size" style="font-weight:600;text-transform:uppercase;letter-spacing:0.08em"><?php echo esc_html_x( 'Membership', 'Pattern eyebrow text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontSize":"2xl"} -->
<h2 class="wp-block-heading has-2-xl-font-size"><?php echo esc_html_x( 'Forty-nine pounds a year', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"neutral-700"} -->
<p class="has-neutral-700-color has-text-color"><?php echo esc_html_x( 'It pays for itself in about three orders. Cancel whenever you like and we refund the months you have not used.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html_x( 'Join now', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html_x( 'Compare levels', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
<div class="wp-block-column is-vertically-aligned-center">
<?php foreach ( $sm_perks as $sm_perk ) : ?>
<!-- wp:suitemart/infobox {"icon":"<?php echo esc_attr( $sm_perk['icon'] ); ?>","iconSize":28,"title":"<?php echo esc_attr( $sm_perk['title'] ); ?>","description":"<?php echo esc_attr( $sm_perk['text'] ); ?>","orientation":"horizontal","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}},"border":{"radius":"var:custom|radius|md"}},"backgroundColor":"neutral-100"} /-->
<?php endforeach; ?>
</div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
