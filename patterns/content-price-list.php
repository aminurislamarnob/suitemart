<?php
/**
 * Title: Price list
 * Slug: suitemart/content-price-list
 * Categories: suitemart/content, services
 * Description: Named items with dotted leaders and prices — a menu or rate card.
 * Keywords: menu, price list, services, rates, tariff
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_items = array(
	array( _x( 'Standard delivery', 'Pattern item', 'suitemart' ), _x( 'Two to four working days, tracked.', 'Pattern text', 'suitemart' ), '£3.95', '', '' ),
	array( _x( 'Next-day delivery', 'Pattern item', 'suitemart' ), _x( 'Order before 3pm, Monday to Friday.', 'Pattern text', 'suitemart' ), '£6.95', '£8.95', _x( 'Popular', 'Pattern badge', 'suitemart' ) ),
	array( _x( 'Collection', 'Pattern item', 'suitemart' ), _x( 'Ready within two hours at the counter.', 'Pattern text', 'suitemart' ), _x( 'Free', 'Pattern price', 'suitemart' ), '', '' ),
);
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"},"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Delivery options', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<?php foreach ( $sm_items as $sm_item ) : ?>
<!-- wp:suitemart/menu-price {"title":"<?php echo esc_attr( $sm_item[0] ); ?>","description":"<?php echo esc_attr( $sm_item[1] ); ?>","price":"<?php echo esc_attr( $sm_item[2] ); ?>","oldPrice":"<?php echo esc_attr( $sm_item[3] ); ?>","badge":"<?php echo esc_attr( $sm_item[4] ); ?>"} /-->
<?php endforeach; ?>
</div>
<!-- /wp:group -->
