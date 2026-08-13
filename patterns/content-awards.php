<?php
/**
 * Title: Awards and certifications
 * Slug: suitemart/content-awards
 * Categories: suitemart/content, featured, about
 * Description: A centred row of awards, each an icon above the year it was given.
 * Keywords: awards, certifications, recognition, accreditation, trust
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_awards = array(
	array(
		'icon'  => 'award',
		'title' => _x( 'Retailer of the year', 'Pattern heading', 'suitemart' ),
		'text'  => _x( 'Independent Trade Awards, 2025', 'Pattern text', 'suitemart' ),
	),
	array(
		'icon'  => 'shield',
		'title' => _x( 'Verified secure checkout', 'Pattern heading', 'suitemart' ),
		'text'  => _x( 'Audited annually since 2019', 'Pattern text', 'suitemart' ),
	),
	array(
		'icon'  => 'globe',
		'title' => _x( 'Carbon-neutral delivery', 'Pattern heading', 'suitemart' ),
		'text'  => _x( 'Offset on every parcel, verified', 'Pattern text', 'suitemart' ),
	),
	array(
		'icon'  => 'star',
		'title' => _x( 'Rated excellent', 'Pattern heading', 'suitemart' ),
		'text'  => _x( 'Across four thousand reviews', 'Pattern text', 'suitemart' ),
	),
);
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}},"border":{"top":{"color":"var:preset|color|neutral-200","width":"1px","style":"solid"},"bottom":{"color":"var:preset|color|neutral-200","width":"1px","style":"solid"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="border-top-color:var(--wp--preset--color--neutral-200);border-top-style:solid;border-top-width:1px;border-bottom-color:var(--wp--preset--color--neutral-200);border-bottom-style:solid;border-bottom-width:1px;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"grid","columnCount":4,"minimumColumnWidth":"14rem"}} -->
<div class="wp-block-group alignwide">
<?php foreach ( $sm_awards as $sm_award ) : ?>
<!-- wp:suitemart/infobox {"icon":"<?php echo esc_attr( $sm_award['icon'] ); ?>","iconSize":40,"title":"<?php echo esc_attr( $sm_award['title'] ); ?>","description":"<?php echo esc_attr( $sm_award['text'] ); ?>","orientation":"vertical","alignment":"center"} /-->
<?php endforeach; ?>
</div>
<!-- /wp:group --></div>
<!-- /wp:group -->
