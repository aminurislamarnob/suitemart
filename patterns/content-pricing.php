<?php
/**
 * Title: Pricing plans
 * Slug: suitemart/content-pricing
 * Categories: suitemart/content, services, call-to-action
 * Description: Three plans side by side, with the middle one highlighted.
 * Keywords: pricing, plans, tiers, subscription, membership
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_plans = array(
	array(
		'name'     => _x( 'Starter', 'Pattern plan name', 'suitemart' ),
		'price'    => '9',
		'summary'  => _x( 'For a first shop finding its feet.', 'Pattern text', 'suitemart' ),
		'badge'    => '',
		'featured' => false,
	),
	array(
		'name'     => _x( 'Growth', 'Pattern plan name', 'suitemart' ),
		'price'    => '29',
		'summary'  => _x( 'For a catalogue that has outgrown a spreadsheet.', 'Pattern text', 'suitemart' ),
		'badge'    => _x( 'Most popular', 'Pattern badge', 'suitemart' ),
		'featured' => true,
	),
	array(
		'name'     => _x( 'Scale', 'Pattern plan name', 'suitemart' ),
		'price'    => '79',
		'summary'  => _x( 'For multi-warehouse and multi-currency selling.', 'Pattern text', 'suitemart' ),
		'badge'    => '',
		'featured' => false,
	),
);
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns">
<?php foreach ( $sm_plans as $sm_plan ) : ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/pricing-table {"planName":"<?php echo esc_attr( $sm_plan['name'] ); ?>","currency":"£","price":"<?php echo esc_attr( $sm_plan['price'] ); ?>","period":"<?php echo esc_attr_x( '/month', 'Pattern billing period', 'suitemart' ); ?>","summary":"<?php echo esc_attr( $sm_plan['summary'] ); ?>","badge":"<?php echo esc_attr( $sm_plan['badge'] ); ?>","featured":<?php echo $sm_plan['featured'] ? 'true' : 'false'; ?>,"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","right":"var:preset|spacing|40","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40"}},"border":{"radius":"10px","width":"1px"}},"borderColor":"neutral-200"} -->
<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li><?php echo esc_html_x( 'Unlimited products', 'Pattern list item', 'suitemart' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( 'Abandoned basket recovery', 'Pattern list item', 'suitemart' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( 'Priority support', 'Pattern list item', 'suitemart' ); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"width":100} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link wp-element-button"><?php echo esc_html_x( 'Choose plan', 'Pattern button', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
<!-- /wp:suitemart/pricing-table --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
